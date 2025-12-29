<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function ContactCreate(ContactCreateRequest $request): JsonResponse{
        $user = Auth::user();
        $data = $request->validated();
        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();
        return (new ContactResource($contact))->response()->setStatusCode(201);
    }
    public function ContactGet(int $id): ContactResource{
        $user = Auth::user();
        $contacts = Contact::where("id", $id)->where("user_id", $user->id)->first();
        if(!$contacts){
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => ["Contact not found"]
                ]
            ], 404));
        }
        return new ContactResource($contacts);
    }
    public function ContactUpdate(int $id, ContactCreateRequest $request): ContactResource{
        $user = Auth::user();
        $contacts = Contact::where("id", $id)->where("user_id", $user->id)->first();
        if(!$contacts){
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => ["Contact not found"]
                ]
            ], 404));
        }
        $data = $request->validated();
        $contacts->update($data);
        return new ContactResource($contacts);
    }
    public function ContactRemove(int $id): JsonResponse{
        $user = Auth::user();
        $contacts = Contact::where("id", $id)->where("user_id", $user->id)->first();
        if(!$contacts){
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => ["Contact not found"]
                ]
            ], 404));
        }
        $contacts->delete();
        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }
    public function ContactSearch(Request $request): ContactCollection{
        $user = Auth::user();
        $page = $request->input("page", 1);
        $size = $request->input("size", 10);
        
        $contacts = Contact::query()->where('user_id', $user->id);
        $contacts = $contacts->where(function (Builder $builder) use ($request){
            $name = $request->input("name");
            if($name){
                $builder->where(function (Builder $builder) use ($name){
                    $builder->orWhere('first_name', 'like', '%'.$name.'%');
                    $builder->orWhere('last_name', 'like', '%'.$name.'%');
                });
            }

            $email = $request->input("email");
            if($email){
                $builder->where('email', 'like', '%'.$email.'%');
            }

            $phone = $request->input("phone");
            if($phone){
                $builder->where('phone', 'like', '%'.$phone.'%');
            }
        });

        $contacts = $contacts->paginate($size, ["*"], "page", $page);
        return new ContactCollection($contacts);
    }
}
