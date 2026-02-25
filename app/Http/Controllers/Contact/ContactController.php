<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    //

    public function index(Request $request){

        return view('site.contact.contact');
    }


}
