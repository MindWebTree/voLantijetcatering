<?php

namespace Webkul\RKREZA\Contact\Http\Controllers;

use App\Jobs\SendContactUsMail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Webkul\RKREZA\Contact\Repositories\ContactRepository as Contact;

class ContactController extends Controller
{

    protected $_config;

    protected $contact;

    public function __construct(Contact $contact)
    {
        $this->contact              = $contact;
        $this->_config              = request('_config');
    }


    public function show()
    {
        return view($this->_config['view']);
    }


    public function index()
    {
        $contact        = $this->contact->all();
        return view($this->_config['view'], compact('contact'));
    }


    public function view($id)
    {

        $contact = $this->contact->findOrFail($id);

        return view($this->_config['view'], compact('contact'));
    }


    public function destroy($id=null)
    {
        
        $contact = $this->contact->findorFail($id);

        try {
            $this->contact->delete($id);

            session()->flash('success', trans('contact_lang::app.response.delete-success', ['name' => 'Message']));

        } catch(\Exception $e) {
            session()->flash('error', trans('contact_lang::app.response.delete-failed', ['name' => 'Message']));
        }


        return response()->json(['message' => false], 400);
    
    }




    // Shop Section
    public function sendMessage()
    {
        $this->validate(request(), [
            'name'              => 'required',
            'email'             => 'required',
            'message_body'      => 'required',
            'g-recaptcha-response' => 'required|captcha',
        ]);

        $data = request()->all();

        

        try {
            $contact = $this->contact->create([
                'name'          => $data['name'],
                'email'         => $data['email'],
                'message_body'  => $data['message_body']
            ]);

            if ($contact) {
                SendContactUsMail::dispatch($data['message_body'], $data['name'],$data['email']);
                session()->flash('success', trans('Thank you for reaching out to us! We’ve received your message and will get back to you as soon as possible'));
                return redirect()->route($this->_config['redirect']);
            }
        }catch (\Exception $e) {

        }

            
    }

    


}
