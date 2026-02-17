<?php

declare(strict_types=1);

namespace App\Profile;

use App\Database;
use App\Utils\SessionUser;

class UpdateProfileAction
{
    public ?string $error = null;

    public function __construct(private readonly UpdateProfileRequest $request, private readonly SessionUser $currentUser)
    {
    }

    public function execute(): bool
    {
        if ($this->request->userId !== $this->currentUser->id) {
            $this->error = 'Unauthorized to update this profile.';
            return false;
        }

        if ($error = $this->request->validate()) {
            $this->error = $error;
            return false;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE users SET display_name = :display_name WHERE id = :user_id');
        return $stmt->execute(['user_id' => $this->request->userId, 'display_name' => $this->request->displayName ?: null]);
    }
}
