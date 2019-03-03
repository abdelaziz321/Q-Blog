<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageSent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Repositories\Chat\RepositoryInterface as ChatRepo;
use App\Repositories\User\AuthRepositoryInterface as AuthUserRepo;

class ChatController extends Controller
{
    private $chatRepo;

    public function __construct(ChatRepo $chatRepo)
    {
        $this->chatRepo = $chatRepo;
    }

    /**
     * get the chat messages.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function fetchMessages(Request $request)
    {
        $messages = $this->chatRepo->getPaginatedMessages(
            $request->query('limit', 5),
            $request->query('id', 0)
        );

        return MessageResource::collection($messages);
    }

    /**
     * add a new message to the authors chat.
     *
     * @param string $_GET['message']
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendMessage(Request $request, AuthUserRepo $authUserRepo)
    {
        $this->authorize('viewUsers', 'App\\User');

        $body = $request->validate([
            'message' => 'required|string'
        ])['message'];

        $message = $this->chatRepo->create($body);

        broadcast(
            new MessageSent($authUserRepo->user(), $message)
        )->toOthers();

        return new MessageResource($message);
    }
}
