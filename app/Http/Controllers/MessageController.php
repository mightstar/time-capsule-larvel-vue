<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Resources\MessageResource;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Http\Requests\DestroyMessageRequest;
use Illuminate\Support\Facades\Log;
use App\Services\Message\MessageService;


class MessageController extends Controller
{
    /**
     * The service instance
     * @var MessageService
     */
    private MessageService $messageService;

    /**
     * Constructor
     */
    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function index(Request $request)
    {
        return $this->messageService->index($request->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function create()
    {
        return $this->responseDataSuccess();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreMessageRequest  $request
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(StoreMessageRequest $request)
    {

        $input = $request->validated();
        $record = $this->messageService->create($input);
        if (!is_null($record)) {
            return $this->responseStoreSuccess(['record' => $record]);
        } else {
            return $this->responseStoreFail();
        }

    }

    /**
     *  Show the form for editing the specified resource.
     *
     * @param  Message  $message
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Message $message)
    {
        if($this->isLocked($message)) {
            return response()->json(['message' => 'This capsule is not yet available.'], 422);
        }

        $model = $this->messageService->get($message);
        $this->messageService->update($message, ['is_opened'=> true]);

        return $this->responseDataSuccess(['model' => $model]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Message  $message
     *
     * @return JsonResponse|\Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function edit(Message $message)
    {

        return $this->show($message);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateMessageRequest  $request
     * @param  Message  $message
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateMessageRequest $request, Message $message)
    {
        $data = $request->validated();

        if($this->isLocked($message)) {
            return response()->json(['message' => 'This capsule is not yet available.'], 422);
        }

        if ($this->messageService->update($message, $data)) {
            return $this->responseUpdateSuccess(['record' => $message->fresh()]);
        } else {
            return $this->responseUpdateFail();
        }
        return response()->json($message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(DestroyMessageRequest $request, Message $message)
    {
        if($this->isLocked($message)) {
            return response()->json(['message' => 'This capsule is not yet available.'], 422);
        }

        if ($this->messageService->delete($message)) {
            return $this->responseDeleteSuccess(['record' => $message]);
        }

        return $this->responseDeleteFail();

    }

    /**
     * Check message's lock status
     *
     * @param  Message  $message
     *
     * @return boolean
     * @throws AuthorizationException
     */
    public function isLocked(Message $message)
    {
        if (!$message->is_opened && $message->scheduled_opening_time->isFuture()) {
            return response()->json(['message' => 'This capsule is not yet available.'], 422);
        }
    }
}
