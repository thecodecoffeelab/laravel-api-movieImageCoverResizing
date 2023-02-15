<?php
namespace App\Http\Controllers\V1;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResizeImageMovieRequest;
//use App\Http\Requests\UpdateMovieManipulationRequest;
use App\Http\Resources\V1\MovieManipulationResource;
use App\Models\MovieManipulation;
//use Intervention\Image\Facades\Image as Image;
//use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManagerStatic as Image;
//use Intervention\Image\ImageManager;
//use Intervention\Image\Facades\make;
use Illuminate\Http\Request;

class MovieManipulationController extends Controller
{
    /**
     * Display a listing of the resource. | Movies
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
      return MovieManipulationResource::collection(CoverManipulation::where('user_id', $request->user()->id)->paginate());
    }

    //GET ALL MOVIE COVERS BY MOVIE
    public function byMovie(Request $request, Movie $movie) 
    {
        if($request->user()->id != $movie->user_id) {
            return abort(403, 'Your are Unauthorized');
        }
        $where = [
            'movie_id' => $movie->id,
        ];
        return MovieManipulationResource::collection(CoverManipulation::where($where)->paginate());
    
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ResizeImageMovieRequest  $request
     * @return \Illuminate\Http\Response
     */
    // Resize all pictures of our Movies
    public function resize(ResizeImageMovieRequest $request)
    {
        $allmovies = $request->all();

        /** @var UploadFile|string $cover */
        $cover = $allmovies['cover'];
        unset($allmovies['cover']);
        $data = [
            'type' => MovieManipulation::TYPE_RESIZE,
            'data' => json_encode($allmovies),
            'user_id' => $request->user()->user_id,
        ];

        if(isset($allmovies['movie_id'])) {
            $movie = Movie::find($allmovies['movie_id']);
            if($request->user()->id != $movie->user_id) {
                return abort(403, 'Your are Unauthorized');
            }
            $data['movie_id'] = $allmovies['movie_id'];
        }
        //Generating public images dir path for images in a random dir
        //images/dshjdjsk/movie.jpg -> images/dshjdjsk/movie-resized.jpg 
        $dir = 'images/'.Str::random().'/';
        $absolutePath = public_path($dir);
        File::makeDirectory($absolutePath);

        if($cover instanceof UploadedFile) {
            $data['name'] = $cover->getClientOriginalName();
            //concatenation to get resized movie cover picture: movie.jpg -> movie-resized.jpg
            $filename = $pathinfo($data['name'], PATHINFO_FILENAME);
            $extension = $cover->getClientOriginalExtension();
            $originalPath = $absolutePath.$data['name'];

            $cover->move($absolutePath, $data['name']);
        } else {
            $data['name'] = pathinfo($cover, PATHINFO_BASENAME);
            $filename = pathinfo($cover, PATHINFO_FILENAME);
            $extension = pathinfo($cover, PATHINFO_EXTENSION);
            //let's define an original path
            $originalPath = $absolutePath.$data['name'];

            //save cover image URL inside the absolute path
            copy($cover, $originalPath);
        }
        $data['path'] = $dir.$data['name'];

        //calculating the value of heigh which is optional based on the given value of width
        $width = $allmovies['width'];
        $height = $allmovies['height'] ?? false;

        //function of calculation generating an array list 
        list($width, $height, $cover) = $this->getCoverWidthAndHeight($width, $height, $originalPath);
        
        $resizedFilename = $filename.'-resized.'.$extension;

        $cover->resize($width, $height)->save($absolutePath.$resizedFilename);
        $data['output_path'] = $dir.$resizedFilename;

        $coverManipulation = MovieManipulation::create($data);

        return new MovieManipulationResource($coverManipulation);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MovieManipulation  $movieManipulation
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, MovieManipulation $movie)
    {
        if($request->user()->id != $movie->user_id) {
            return abort(403, 'Your are Unauthorized');
        }
        return new MovieManipulationResource($movie);
    }

 /*    /** UPDATE NOT NEEDED FOR IMAGE MANIPULATION
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMovieManipulationRequest  $request
     * @param  \App\Models\MovieManipulation  $movieManipulation
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMovieManipulationRequest $request, MovieManipulation $movieManipulation)
    {
        //
    } 

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MovieManipulation  $movieManipulation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, MovieManipulation $movie)
    {
        if($request->user()->id != $movie->user_id) {
            return abort(403, 'Your are Unauthorized');
        }
        $movie->delete();
        return response('', 204);
    }

        //Method calculating Width And Heigh Movie Cover Image | Using Intervention Image -> Best librarie for Image Manipulations
        protected function getCoverWidthAndHeight($width, $height, string $originalPath) 
        {
             $cover = Image::make($originalPath);
             $originalWidth = $cover->width();
             $originalHeight = $cover->height();

             if(str_ends_with($width, '%')) {
                $ratioW = (float)str_replace('%', '', $width);
                $ratioH = $height ? (float)str_replace('%', '', $height) : $ratioW;

                $newWidth = $originalWidth * $ratioW / 100;
                $newHeight = $originalHeight * $ratioH / 100;
             } else {
                $newWidth = (float)$width;
                /**Formula
                 * $originalWidth - $newWidth
                 * $originalHeight - $newHeight
                 * -----------------------------
                 * $newHeight = $originalHeight * $newWidth/$originalWidth
                 */

                 $newHeight = $height ? (float)$height : $originalHeight * $newWidth/$originalWidth;
                
             }

             return [$newWidth, $newHeight, $cover];
        }
}
