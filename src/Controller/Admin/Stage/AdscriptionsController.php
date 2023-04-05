<?php

declare(strict_types=1);

namespace App\Controller\Admin\Stage;

use App\Controller\Admin\AppAdminController;
use App\Controller\Traits\RedirectLogicTrait;
use App\Model\Field\AdscriptionStatus;
use App\Model\Field\StageField;
use App\Model\Field\StageStatus;
use App\Utility\Stages;
use Cake\Http\Exception\ForbiddenException;
use Cake\Log\Log;

/**
 * StudentAdscriptionsController Controller
 *
 * @property \App\Model\Table\StudentAdscriptionsTable $StudentAdscriptions
 * @method \App\Model\Entity\StudentAdscription[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AdscriptionsController extends AppAdminController
{
    use RedirectLogicTrait;

    public function initialize(): void
    {
        parent::initialize();
        $this->StudentStages = $this->fetchTable('StudentStages');
        $this->StudentAdscriptions = $this->fetchTable('StudentAdscriptions');
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add($student_id = null)
    {
        $student = $this->StudentAdscriptions->Students->get($student_id);
        $student_adscription = $this->StudentAdscriptions->newEmptyEntity();
        if ($this->request->is('post')) {
            try {
                $this->StudentStages->getConnection()->begin();

                $student_adscription = $this->StudentAdscriptions->patchEntity($student_adscription, $this->request->getData());
                $this->StudentAdscriptions->saveOrFail($student_adscription);

                $adscriptionStage = $this->StudentStages
                    ->find('byStudentStage', [
                        'student_id' => $student->id,
                        'stage' => StageField::ADSCRIPTION,
                    ])
                    ->first();
                $this->StudentStages->updateStatus($adscriptionStage, StageStatus::IN_PROGRESS);

                $this->StudentStages->getConnection()->commit();

                $this->Flash->success(__('The student_adscription has been saved.'));

                return $this->redirect(['_name' => 'admin:student:view', $student_id]);
            } catch (\Exception $e) {
                $this->StudentStages->getConnection()->rollback();
                Log::error($e->getMessage());
                $this->Flash->error(__('The student_adscription could not be saved. Please, try again.'));
            }
        }

        $institution_projects = $this->StudentAdscriptions->InstitutionProjects
            ->find('list', [
                'groupField' => 'institution.name',
                'limit' => 200
            ])
            ->contain(['Institutions'])
            ->where([
                'Institutions.tenant_id' => $student->tenant_id,
            ]);

        $tutors = $this->StudentAdscriptions->Tutors
            ->find('list', ['limit' => 200])
            ->where([
                'Tutors.tenant_id' => $student->tenant_id,
            ]);
        
        $back = $this->getRedirectUrl();

        $this->set(compact('student', 'student_adscription', 'institution_projects', 'tutors', 'back'));
    }

    /**
     * Edit method
     *
     * @param string|null $id StudentAdscription id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $adscription = $this->StudentAdscriptions->get($id, [
            'contain' => [
                'InstitutionProjects' => ['Institutions'],
                'Tutors',
                'Students'
            ],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $adscription = $this->StudentAdscriptions->patchEntity($adscription, $this->request->getData());
            if ($this->StudentAdscriptions->save($adscription)) {
                $this->Flash->success(__('The student_adscription has been saved.'));

                return $this->redirect(['_name' => 'admin:student:adscriptions', $adscription->student_id]);
            }
            $this->Flash->error(__('The student_adscription could not be saved. Please, try again.'));
        }

        $tutors = $this->StudentAdscriptions->Tutors
            ->find('list', ['limit' => 200])
            ->where([
                'Tutors.tenant_id' => $adscription->student->tenant_id,
            ]);

        $this->set(compact('adscription', 'tutors'));
    }

    public function changeStatus($status, $id)
    {
        $this->request->allowMethod(['post', 'put']);
        $adscription = $this->StudentAdscriptions->get($id);

        if ($status == AdscriptionStatus::VALIDATED->value && !$this->Authorization->can($adscription, 'validate')) {
            throw new ForbiddenException('No tiene permisos para validar la adscripción');
        }

        $adscription->status = $status;
        if ($this->StudentAdscriptions->save($adscription)) {
            $this->Flash->success(__('The student_adscription has been saved.'));
        } else {
            $this->Flash->error(__('The student_adscription could not be saved. Please, try again.'));
        }

        if ($status == AdscriptionStatus::OPEN->value) {
            Stages::closeStudentStage($adscription->student_id, StageField::ADSCRIPTION, StageStatus::SUCCESS);
        }

        return $this->redirect(['controller' => 'Students', 'action' => 'adscriptions', $adscription->student_id, 'prefix' => 'Admin']);
    }
}
