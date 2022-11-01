<?php
declare(strict_types=1);

namespace App\Stage;

use App\Model\Entity\Student;
use App\Model\Entity\StudentStage;
use Cake\Datasource\EntityInterface;

interface StageInterface
{
    public function __construct(StudentStage $studentStage);
    public function getStageKey(): string;
    public function getStudentId(): int;
    public function getStudent(bool $reset = false): ?Student;
    public function getStudentStage(bool $reset = false): ?StudentStage;
    public function getLastError(): string;
    public function setLastError(string $error);
    public function getNextStageKey(): string;
    public function defaultValues(): array;
    public function initialize(): void;
    public function close(string $status);    
}
