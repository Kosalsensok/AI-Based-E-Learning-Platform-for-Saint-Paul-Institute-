<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;

class AbaPaymentService
{
    public function generatePaymentLink(Course $course, User $student): string
    {
        $tranId = 'ABA-' . strtoupper(uniqid());
        return "https://checkout.payway.com.kh/pay?amount={$course->price}&tran_id={$tranId}";
    }
}
