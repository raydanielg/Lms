<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\sendContactReply;
use App\Mail\SendNotifications;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $this->authorize('admin_contacts_lists');

        $contacts = Contact::orderBy('status', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/pages/users.contacts_lists'),
            'contacts' => $contacts
        ];

        return view('admin.contacts.lists', $data);
    }

    public function reply($id)
    {
        $this->authorize('admin_contacts_reply');

        $contact = Contact::findOrFail($id);

        $data = [
            'pageTitle' => trans('admin/main.reply'),
            'contact' => $contact
        ];

        return view('admin.contacts.reply', $data);
    }

    public function storeReply(Request $request, $id)
    {
        $this->authorize('admin_contacts_reply');

        $this->validate($request, [
            'reply' => 'required'
        ]);

        $reply = $request->get('reply');

        $contact = Contact::findOrFail($id);
        $contact->reply = $reply;
        $contact->status = 'replied';
        $contact->save();

        if (!empty($contact->email)) {
            \Mail::to($contact->email)->send(new sendContactReply($contact));
        }

        return redirect(getAdminPanelUrl().'/contacts');
    }

    public function delete($id)
    {
        $this->authorize('admin_contacts_delete');

        $contact = Contact::findOrFail($id);

        $contact->delete();

        return redirect(getAdminPanelUrl().'/contacts');
    }
}
