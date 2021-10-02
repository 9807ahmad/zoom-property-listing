<?php

namespace App\Http\Controllers;

use App\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class PropertyItemController extends Controller
{
    /**
     * Show the property page
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function index()
    {
        return view('property-home');
    }

    /**
     * Returns properties as json response
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function properties(Request $request)
    {
        return response()->json(Property::orderBy('id', 'desc')->get());
    }

    /**
     * Creates and stores property to database.
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        
        /**
         * Valiate post data
         */
        $this->validate($request, [
            'title' => [ 'required',  'string', 'min:5', 'max:50' ],
            'description' => [ 'required',  'string', 'min:5', 'max:50' ],
            'price' => [ 'required'],
            'file' => 'required|mimes:jpeg,jpg,png|max:2048',
        ]); 

        $fileName = time().'.'.$request->file->extension(); 
        $request->file->move(public_path('uploads'), $fileName); 

        $popertyItem = new Property();
        $popertyItem->title = $request->title;
        $popertyItem->description = $request->description;
        $popertyItem->price = $request->price;
        $popertyItem->image = $fileName;
        $popertyItem->save();

        return $popertyItem;
    }

  
}
