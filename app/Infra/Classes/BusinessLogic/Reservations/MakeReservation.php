<?php

namespace App\Infra\Classes\BusinessLogic\Reservations;

use App\Infra\Classes\Common\Constants;
use App\Infra\Interfaces\Repositories\ReservationsRepositoryInterface;
use App\Infra\Interfaces\Repositories\TablesRepositoryInterface;
use App\Infra\Interfaces\Validators\ValidatorInterface;
use App\Infra\Traits\TriggerErrorsTrait;
use Illuminate\Support\Facades\Auth;

class MakeReservation
{
    use TriggerErrorsTrait;

    private ValidatorInterface $validator;
    private TablesRepositoryInterface $tablesRepository;
    private ReservationsRepositoryInterface $reservationsRepository;
    private int $status;

    public function __construct(ValidatorInterface              $validator, TablesRepositoryInterface $tablesRepository,
                                ReservationsRepositoryInterface $reservationsRepository)
    {
        $this->validator = $validator;
        $this->tablesRepository = $tablesRepository;
        $this->reservationsRepository = $reservationsRepository;
    }

    public function validate(): bool
    {
        $this->validator->integer(['capacity'])->min('capacity', 1);
        $this->validator->matchDateFormat('from', Constants::RESERVATION_DATE_FORMAT);
        $this->validator->matchDateFormat('to', Constants::RESERVATION_DATE_FORMAT);
        return $this->validator->validate();
    }

    public function getErrors(): array
    {
        return array_merge($this->errors, $this->validator->getErrors());
    }

    public function make()
    {
        return $this->reservationsRepository->create([
            'customer_id' => Auth::guard('customers')->id(),
            'table_id' => $this->findTableAndSetStatus(),
            'from' => $this->validator->getResource()->get('from'),
            'to' => $this->validator->getResource()->get('to'),
            'status' => $this->status
        ]);
    }

    private function findTableAndSetStatus(): int
    {
        $table = $this->tablesRepository->forActiveReservation(
            $this->validator->getResource()->get('from'),
            $this->validator->getResource()->get('to'),
            $this->validator->getResource()->get('capacity')
        );
        if ($table) {
            $this->status = ReservationStatuses::RESERVATION_ACTIVE;
        } else {
            $table = $this->tablesRepository->forWaitingReservation(
                $this->validator->getResource()->get('from'),
                $this->validator->getResource()->get('to'),
                $this->validator->getResource()->get('capacity')
            );
            if (!$table) {
                $table['id'] = $this->tablesRepository->forOverWaitingReservation(
                    $this->validator->getResource()->get('from'),
                    $this->validator->getResource()->get('to'),
                    $this->validator->getResource()->get('capacity')
                );
            }
            $this->status = ReservationStatuses::RESERVATION_WAITING;
        }
        return $table['id'];
    }
}
