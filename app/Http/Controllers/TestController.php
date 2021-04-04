<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
//date_default_timezone_set('America/Los_Angeles');

class TestController extends Controller
{
    /*public function test(){
        return response()->json(['0'=>0,'1'=>1,'msg'=>'msg']);
    }
    */

    public function registerTemporelle(Request $request){
        //id	name	prename	age	email	email_verified_at	password	poste
         User::create([
            'name'=>'tuteur 1',
            'prename'=>'',
            'type'=>'tuteur',
            'email'=>'tuteur1@tuteur1.com',
            'password'=>bcrypt('tuteur1')
            ]);

             User::create([
                'name'=>'tuteur 2',
                'prename'=>'',
                'type'=>'tuteur',
                'email'=>'tuteur2@tuteur2.com',
                'password'=>bcrypt('tuteur2')
                ]);

                User::create([
                    'name'=>'tuteur 3',
                    'prename'=>'',
                    'type'=>'tuteur',
                    'email'=>'tuteur3@tuteur3.com',
                    'password'=>bcrypt('tuteur3')
                    ]);
        

         User::create([
            'name'=>'client 1',
            'prename'=>'',
            'type'=>'client',
            'email'=>'client1@client1.com',
            'password'=>bcrypt('client1')
            ]);

             User::create([
                'name'=>'client 2',
                'prename'=>'',
                'type'=>'client',
                'email'=>'client2@client2.com',
                'password'=>bcrypt('client2')
                ]);

             User::create([
                    'name'=>'client 3',
                    'prename'=>'',
                    'type'=>'client',
                    'email'=>'client3@client3.com',
                    'password'=>bcrypt('client3')
                    ]);

    }

    public function test(){
        return response()->json(['auth'=>true,'msg'=>'authenthifiecated 1']);
    }
}
