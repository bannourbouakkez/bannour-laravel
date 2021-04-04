<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;
use JWTAuth;


class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
        //$this->middleware('jwt.auth', ['except' => ['login','refresh','register','registerTemporelle']]);
        //$this->middleware('jwt.refresh')->only('refresh');   
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        
        
        $credentials = request(['email', 'password']);
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }


        $token=JWTAuth::claims(['id'=>auth()->user()->id,'name'=>auth()->user()->name,'type' =>auth()->user()->type])->attempt($credentials);
        
        return $this->respondWithToken($token);
        

        /*
        $credentials = request(['email', 'password']);
        //if (! $token = JWTAuth::claims(['who' => 'admin'])->attempt($credentials)) {
          
          if (!JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
          }
        
        
        $posts_arr=user_post::select('post_id')->where('user_id','=',JWTAuth::user()->id)->get();
        $posts="";
        foreach($posts_arr as $poste){
           $PostID=$poste['post_id'];
           $postName=post::select('post')->where('PostID','=',$PostID)->first();
           $postName=$postName->post;
           $posts.=$postName.',';

        }
       
        $token=JWTAuth::claims(['id'=>JWTAuth::user()->id,'name'=>'','Posts' =>$posts])->attempt($credentials);
        return $this->respondWithToken($token);
        */


        
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
        
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
        //return response()->json([ 'jwt'=>$token,'refreshToken'=>'']);
    }


    public function test(){
        return response()->json(['0'=>0,'1'=>1,'msg'=>'msg']);
    }

}