<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListingController extends Controller
{
    // Common Resources routes
    // index - show all listing
    // show - show single listing
    // create - show form to create new listing
    // store - store new listing 
    // edit - show form to edit listing
    // update - update listing
    // destroy - delete listing

    //All listings
    public function index(){
        return view('listings.index', ['listings' => Listing::latest()->filter
        (request(['tag', 'search']))->paginate(6)]);
    }

    //single listing
    public function show(Listing $listing){
        return view('listings.show', ['listing' => $listing]);
    }

    //show create form
     public function create(){
        return view('listings.create');
    }

    //store listing data
    public function store(Request $request){
        $formFields = $request->validate([
            'title' => 'required',
            'company' => ['required', Rule::unique('listings', 'company')],
            'location' => 'required',
            'website' => 'required',
            'email' => ['required', 'email'],
            'tags' => 'required',
            'description' => 'required',
        ]);

        if($request->hasFile('logo')){
            $formFields['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $formFields['user_id'] = auth()->id();

        Listing::create($formFields);

        return redirect("/")->with('message', 'listing created successfully!');
    }

    //show Edit form
    public function edit(Listing $listing){
        return view('listings.edit', ['listing' => $listing]);
    }

    //store listing data
    public function update(Request $request, Listing $listing){
        // Make sure logged in user is owner
        if($listing->yser_id != auth()->id()){
            abort(403, 'Unauthorized Action');
        }

        $formFields = $request->validate([
            'title' => 'required',
            'company' => 'required', 
            'location' => 'required',
            'website' => 'required',
            'email' => ['required', 'email'],
            'tags' => 'required',
            'description' => 'required',
        ]);

        if($request->hasFile('logo')){
            $formFields['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $listing->update($formFields);

        return back()->with('message', 'listing updated successfully!');
    }
    
    // Delete listing
    public function destroy(Listing $listing){
        // Make sure logged in user is owner
        if($listing->yser_id != auth()->id()){
            abort(403, 'Unauthorized Action');
        }
        
        $listing->delete();
        return redirect('/')->with('message', 'listing deleted succesfully');
    }

    // Manage listing
    public function manage(){
        return view('listings.manage', ['listings' =>  auth()->user()->listings()->get()]);
    }
}






