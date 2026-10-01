<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EnquiryModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class EnquiryController extends BaseController
{
    public function index()
    {
        $model  = new EnquiryModel();
        $status = (string) $this->request->getGet('status');

        if (in_array($status, EnquiryModel::STATUSES, true)) {
            $model->where('status', $status);
        }

        return view('admin/enquiries/index', [
            'pageTitle' => 'Enquiries',
            'enquiries' => $model->orderBy('id', 'DESC')->paginate(25),
            'pager'     => $model->pager,
            'status'    => $status,
        ]);
    }

    public function show($id)
    {
        $enquiry = (new EnquiryModel())->find($id) ?? throw PageNotFoundException::forPageNotFound();

        return view('admin/enquiries/show', ['pageTitle' => 'Enquiry #' . $id, 'enquiry' => $enquiry]);
    }

    public function status($id)
    {
        $status = (string) $this->request->getPost('status');
        if (! in_array($status, EnquiryModel::STATUSES, true)) {
            return redirect()->back()->with('error', 'Unknown status.');
        }

        (new EnquiryModel())->update($id, ['status' => $status]);

        return redirect()->back()->with('message', 'Marked as ' . $status . '.');
    }

    public function delete($id)
    {
        (new EnquiryModel())->delete($id);

        return redirect()->to('/admin/enquiries')->with('message', 'Enquiry deleted.');
    }
}
