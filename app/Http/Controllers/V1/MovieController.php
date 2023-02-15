<?php

namespace App\Http\Controllers\V1;

use App\Http\Resources\V1\MovieResource; //calling the resource
use App\Http\Controllers\Controller;     //calling controller from Base Controller
//use App\Http\Controllers\V1\MovieManipulationController; 
use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //SHOWNG ALL MOVIES | all() gives the array content & paginate gives extra info like mata and header links and page number
        //we can increase the number of page by : paginate(10); -> number of page = 10
        //   return MovieResource::collection(Movie::all());
        //return MovieResource::collection(Movie::paginate(10));

        return MovieResource::collection(Movie::where('user_id', $request->user()->id)->paginate());
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMovieRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMovieRequest $request)
    {
        //CREATE A MOVIE
        $data = $request->all();
        $data['user_id'] = $request->user()->id;
        $movie = Movie::create($data);
        
        //return a mvie by MovieController
        //return $movie;
        
        //return a movie by MovieResource
        return new MovieResource($movie);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Movie $movie)
    {
        //return a Single movie by MovieController
        //return $movie;

        //return a Single Movie by MovieResource
        //checking if user has authorization to read movie resources
        if($request->user()->id != $movie->user_id) {
            return abort(403, 'Your are Unauthorized');
        }
        return new MovieResource($movie);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMovieRequest  $request
     * @param  \App\Models\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMovieRequest $request, Movie $movie)
    {
         //checking for update authorization
        if($request->user()->id != $movie->user_id) {
            return abort(403, 'Your are Unauthorized');
        }
        //UPDATE A MOVIE By MovieController
        $movie->update($request->all());
        //return $movie;
        
        //return a movie by MovieResource
        return new MovieResource($movie);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Movie $movie)
    {
         //checking for update authorization
         if($request->user()->id != $movie->user_id) {
            return abort(403, 'Your are Unauthorized');
        }
        //DELETE A MOVIE
        $movie->delete();

         return response('', 204);
    }
}
