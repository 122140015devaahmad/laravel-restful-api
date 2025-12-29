<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function AddressCreate(int $idContact, AddressCreateRequest $request): JsonResponse{
       $user = Auth::user(); 
       $contact = Contact::where("id", $idContact)->where("user_id", $user->id)->first();
       if(!$contact){
           throw new HttpResponseException(response([
               "errors" => [
                   "message" => ["Contact not found"]
               ]
           ], 404));
       }
       $data = $request->validated();
       $address = new Address($data);
       $address->contact_id = $contact->id;
       $address->save();
       return (new AddressResource($address))->response()->setStatusCode(201);
    }
    public function AddressGet(int $idContact, int $idAddress): AddressResource {
        $user = Auth::user();
        $contact = Contact::where("id", $idContact)->where("user_id", $user->id)->first();
        if(!$contact){
           throw new HttpResponseException(response([
               "errors" => [
                   "message" => ["Contact not found"]
               ]
           ], 404));
       }
       $address = Address::where("id", $idAddress)->where("contact_id", $contact->id)->first();
       if(!$address){
           throw new HttpResponseException(response([
               "errors" => [
                   "message" => ["Address not found"]
               ]
           ], 404));
       }
       return new AddressResource($address);
    }
    public function AddressUpdate(int $idContact, int $idAddress, AddressCreateRequest $request): AddressResource{
        $user = Auth::user();
        $contact = Contact::where("id", $idContact)->where("user_id", $user->id)->first();
        if(!$contact){
           throw new HttpResponseException(response([
               "errors" => [
                   "message" => ["Contact not found"]
               ]
           ], 404));
        }
        $data = $request->validated();
        $address = Address::where("id", $idAddress)->where("contact_id", $contact->id)->first();
        if(!$address){
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => ["Address not found"]
                ]
            ], 404));
        }
        $address->update($data);
        return new AddressResource($address);
    }
    public function AddressDelete(int $idContact, int $idAddress): JsonResponse{
        $user = Auth::user();
        $contact = Contact::where("id", $idContact)->where("user_id", $user->id)->first();
        if(!$contact){
           throw new HttpResponseException(response([
               "errors" => [
                   "message" => ["Contact not found"]
               ]
           ], 404));
        }
        $address = Address::where("id", $idAddress)->where("contact_id", $contact->id)->first();
        if(!$address){
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => ["Address not found"]
                ]
            ], 404));
        }
        $address->delete();
        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }
    public function AddressList(int $idContact): JsonResponse{
        $user = Auth::user();
        $contact = Contact::where("id", $idContact)->where("user_id", $user->id)->first();
        if(!$contact){
           throw new HttpResponseException(response([
               "errors" => [
                   "message" => ["Contact not found"]
               ]
           ], 404));
        }
        $address = Address::where("contact_id", $contact->id)->get();
        if(!$address){
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => ["Address not found"]
                ]
            ], 404));
        }
        return AddressResource::collection($address)->response()->setStatusCode(200);
    }
}
