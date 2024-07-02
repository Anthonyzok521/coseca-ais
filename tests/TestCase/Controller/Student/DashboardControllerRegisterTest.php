<?php

declare(strict_types=1);

namespace App\Test\TestCase\Controller\Student;

use App\Model\Field\StageField;
use App\Model\Field\StageStatus;
use App\Model\Field\StudentType;
use Cake\I18n\FrozenDate;
use Cake\Utility\Hash;

/**
 * App\Controller\Student\DashboardController Test Case
 *
 * @uses \App\Controller\Student\DashboardController
 */
class DashboardControllerRegisterTest extends StudentTestCase
{
  public function testStudentTypeRegular(): void
  {
    $student = $this->createRegularStudent();
    $this->setAuthSession($student);

    $this->get('/student');
    $this->assertResponseOk();
    $this->assertResponseContains('Registro');
    $this->assertResponseContains('Taller');
    $this->assertResponseContains('Adscripción');
    $this->assertResponseContains('Seguimiento');
    $this->assertResponseContains('Resultados');
    $this->assertResponseContains('Conclusión');
    $this->assertResponseNotContains('Convalidación');

    $this->updateRecord($student, ['type' => StudentType::VALIDATED->value]);
    $this->get('/student');
    $this->assertResponseOk();
    $this->assertResponseContains('Registro');
    $this->assertResponseNotContains('Taller');
    $this->assertResponseNotContains('Adscripción');
    $this->assertResponseNotContains('Seguimiento');
    $this->assertResponseNotContains('Resultados');
    $this->assertResponseNotContains('Conclusión');
    $this->assertResponseContains('Convalidación');
  }

  public function testRegisterCardStatusInProgress(): void
  {
    $student = $this->createRegularStudent(['lapse_id' => null]);
    $this->setAuthSession($student);
    $lapse_id = $this->lapse_id;

    $this->addRecord('StudentStages', [
      'student_id' => $student->id,
      'stage' => StageField::REGISTER->value,
      'status' => StageStatus::IN_PROGRESS->value,
    ]);

    // whitout lapse dates
    $this->get('/student');
    $this->assertResponseOk();
    $this->assertResponseContains('No existe fecha de registro');
    $this->assertResponseContains($this->alertMessage);

    $lapseDate = $this->getRecordByOptions('LapseDates', [
      'lapse_id' => $lapse_id,
      'stage' => StageField::REGISTER->value,
    ]);

    // with lapse dates in pass
    $start_date = FrozenDate::now()->subDays(4);
    $end_date = FrozenDate::now()->subDays(3);
    $this->updateRecord($lapseDate, compact('start_date', 'end_date'));
    $this->get('/student');
    $this->assertResponseOk();
    $this->assertResponseContains('Ya pasó el período de registro');
    $this->assertResponseContains($this->alertMessage);

    // with lapse dates in future
    $start_date = FrozenDate::now()->addDays(3);
    $end_date = FrozenDate::now()->addDays(4);
    $this->updateRecord($lapseDate, compact('start_date', 'end_date'));
    $this->get('/student');
    $this->assertResponseOk();
    $this->assertResponseContains(__('Fecha de registro: {0}', $lapseDate->show_dates));
    $this->assertResponseContains($this->alertMessage);

    // with lapse dates in progress
    $start_date = FrozenDate::now()->subDays(1);
    $end_date = FrozenDate::now()->addDays(1);
    $this->updateRecord($lapseDate, compact('start_date', 'end_date'));
    $this->get('/student');
    $this->assertResponseOk();
    $this->assertResponseContains('Formulario de registro');
  }

  public function testRegisterCardStatusReview(): void
  {
    $student = $this->createRegularStudent();
    $this->setAuthSession($student);
    $this->addRecord('StudentStages', [
      'student_id' => $student->id,
      'stage' => StageField::REGISTER->value,
      'status' => StageStatus::REVIEW->value,
    ]);

    $this->get('/student');
    $this->assertResponseOk();
    $this->assertResponseContains('En espera de revisión de documentos.');
    $this->assertResponseContains($this->alertMessage);
  }

  public function testRegisterCardStatusSuccess(): void
  {
    $student = $this->createRegularStudent();
    $this->setAuthSession($student);
    $this->addRecord('StudentStages', [
      'student_id' => $student->id,
      'stage' => StageField::REGISTER->value,
      'status' => StageStatus::SUCCESS->value,
    ]);

    $this->get('/student');
    $this->assertResponseOk();
    $this->assertResponseContains(Hash::get($this->user, 'dni'));
    $this->assertResponseContains(Hash::get($this->user, 'first_name'));
    $this->assertResponseContains(Hash::get($this->user, 'last_name'));
    $this->assertResponseContains(Hash::get($this->user, 'email'));
    $this->assertResponseContains($this->program->name . ' | ' . $this->program->tenants[0]->location->name);
    $this->assertResponseContains(Hash::get($student, 'student_data.phone'));
  }

  public function testRegisterCardOtherStatuses(): void
  {
    $student = $this->createRegularStudent();
    $this->setAuthSession($student);

    $stageRegistry = $this->addRecord('StudentStages', [
      'student_id' => $student->id,
      'stage' => StageField::REGISTER->value,
      'status' => StageStatus::WAITING->value,
    ]);

    $this->get('/student');
    $this->assertResponseOk();
    $this->assertResponseContains('Sin información a mostrar');
    $this->assertResponseContains($this->alertMessage);

    $this->updateRecord($stageRegistry, ['status' => StageStatus::FAILED->value]);
    $this->get('/student');
    $this->assertResponseOk();
    $this->assertResponseContains('Sin información a mostrar');
    $this->assertResponseContains($this->alertMessage);

    $this->updateRecord($stageRegistry, ['status' => StageStatus::LOCKED->value]);
    $this->get('/student');
    $this->assertResponseOk();
    $this->assertResponseContains('Sin información a mostrar');
    $this->assertResponseContains($this->alertMessage);
  }
}
