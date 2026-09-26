<?php

namespace App\Controllers;

use App\Models\EnquiryModel;

class Enquiry extends BaseController
{
    public function store()
    {
        // Honeypot: bots fill hidden fields, people do not.
        if ($this->request->getPost('ec_website') !== null && $this->request->getPost('ec_website') !== '') {
            return redirect()->to(base_url() . '#contact');
        }

        // Optional limits mirror the column sizes in CreateEnquiries.
        $rules = [
            'name'              => ['label' => 'Name',    'rules' => 'required|min_length[2]|max_length[120]'],
            'email'             => ['label' => 'Email',   'rules' => 'required|valid_email|max_length[180]'],
            'message'           => ['label' => 'Project description', 'rules' => 'required|min_length[20]|max_length[5000]'],
            'company'           => ['label' => 'Company', 'rules' => 'permit_empty|max_length[160]'],
            'phone'             => ['label' => 'Phone',   'rules' => 'permit_empty|max_length[40]'],
            'service'           => ['label' => 'Service', 'rules' => 'permit_empty|max_length[80]'],
            'current_system'    => ['label' => 'Current website or system', 'rules' => 'permit_empty|max_length[255]'],
            'budget'            => ['label' => 'Budget',   'rules' => 'permit_empty|max_length[80]'],
            'timeline'          => ['label' => 'Timeline', 'rules' => 'permit_empty|max_length[80]'],
            'preferred_contact' => ['label' => 'Preferred contact method', 'rules' => 'permit_empty|in_list[Email,Phone,WhatsApp]'],
        ];

        if (! $this->validate($rules)) {
            return redirect()->to(base_url() . '#contact')
                ->withInput()
                ->with('ec_errors', $this->validator->getErrors())
                ->with('ec_error', 'Some details are missing. Check the highlighted fields and send again.');
        }

        $field = fn (string $k): ?string => ($v = trim((string) $this->request->getPost($k))) === '' ? null : $v;

        try {
            (new EnquiryModel())->insert([
                'name'              => $field('name'),
                'company'           => $field('company'),
                'email'             => $field('email'),
                'phone'             => $field('phone'),
                'service'           => $field('service'),
                'current_system'    => $field('current_system'),
                'budget'            => $field('budget'),
                'timeline'          => $field('timeline'),
                'preferred_contact' => $field('preferred_contact'),
                'message'           => $field('message'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Enquiry insert failed: ' . $e->getMessage());

            return redirect()->to(base_url() . '#contact')
                ->withInput()
                ->with('ec_error', 'Your enquiry could not be saved just now. Please try again in a few minutes.');
        }

        return redirect()->to(base_url() . '#contact')
            ->with('ec_success', 'Enquiry received. We will read the detail and reply on your preferred contact method.');
    }
}
