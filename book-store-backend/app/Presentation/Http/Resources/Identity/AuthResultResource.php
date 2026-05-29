<?php

namespace App\Presentation\Http\Resources\Identity;

use App\Application\Identity\UseCases\AuthResult;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/** @property AuthResult $resource */
final class AuthResultResource extends JsonResource
{
    public function __construct(
        private readonly AuthResult $result,
    ) {
        parent::__construct($result);
    }

    public function toArray(Request $request): array
    {
        return [
            'token' => $this->result->token->value,
            'user'  => [
                'id'    => $this->result->user->getId()->value,
                'name'  => $this->result->user->getName(),
                'email' => $this->result->user->getEmail()->value,
                'role'  => $this->result->user->getRole()->value,
            ],
        ];
    }
}
