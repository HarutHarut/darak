<?php

namespace App\Http\Controllers\Api;

use App\Luglocker\Email\EmailCreator;
use Carbon\Carbon;
use \Throwable;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Contact\Create;

class ContactController extends ApiController
{
    public function create(Create $request): JsonResponse
    {
        $data = $request->validated();

        try {

            DB::beginTransaction();

            $contact = Contact::query()
                ->create($data);

            $viewData = [
                'subject' => __('general.emails.contactForm.subject', ['date' => Carbon::now()->subMonth()->format('Y-m')]),
                'name' => $data['name'],
                'lastName' => $data['last_name'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'email' => $data['email'],
                'message' => $data['message'],
            ];

            EmailCreator::create(
                1,
                env('MAIL_FROM_ADDRESS'),
                $viewData['subject'],
                view('emails.contactFormView', $viewData)->render(),
                'emails.contactFormView',
                config('constants.email_type.contact_form')
            );

            DB::commit();
            return $this->success(200, ['contact' => $contact], "Contact created successfully.");
        } catch (\Throwable $e) {
            DB::rollback();
            $this->errorLog($request, $e, 'ContactController create action');
            return $this->error(400, "Contact create failed.");
        }
    }
}
