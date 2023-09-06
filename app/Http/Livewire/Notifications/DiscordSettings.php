<?php

namespace App\Http\Livewire\Notifications;

use App\Models\Team;
use App\Notifications\Test;
use Livewire\Component;

class DiscordSettings extends Component
{
    public Team $team;
    protected $rules = [
        'team.discord_enabled' => 'nullable|boolean',
        'team.discord_webhook_url' => 'required|url',
        'team.discord_notifications_test' => 'nullable|boolean',
        'team.discord_notifications_deployments' => 'nullable|boolean',
        'team.discord_notifications_status_changes' => 'nullable|boolean',
        'team.discord_notifications_database_backups' => 'nullable|boolean',
    ];
    protected $validationAttributes = [
        'team.discord_webhook_url' => 'Discord Webhook',
    ];

    public function mount()
    {
        $this->team = auth()->user()->currentTeam();
    }
    public function instantSave()
    {
        try {
            $this->submit();
        } catch (\Exception $e) {
            ray($e->getMessage());
            $this->team->discord_enabled = false;
            $this->validate();
        }
    }

    public function submit()
    {
        $this->resetErrorBag();
        $this->validate();
        $this->saveModel();
    }

    public function saveModel()
    {
        $this->team->save();
        if (is_a($this->team, Team::class)) {
            refreshSession();
        }
        $this->emit('success', 'Settings saved.');
    }

    public function sendTestNotification()
    {
        $this->team->notify(new Test());
        $this->emit('success', 'Test notification sent.');
    }
}
