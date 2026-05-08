<?php

namespace App\Livewire\Issues;

use App\Exceptions\AIException;
use App\Models\Department;
use App\Models\Issue;
use App\Models\IssueType;
use App\Models\User;
use App\Services\IssueAIService;
use App\Services\IssueService;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Form extends Component
{
    public ?Issue $issue = null;

    #[Rule(['required', 'string', 'max:255'])]
    public string $title = '';

    #[Rule(['nullable', 'string'])]
    public ?string $description = null;

    #[Rule(['required', 'in:urgent,high,medium,low'])]
    public string $priority = 'medium';

    #[Rule(['nullable', 'string', 'max:255'])]
    public ?string $location = null;

    public array $department_ids = [];

    public array $issue_type_ids = [];

    #[Rule(['nullable', 'exists:users,id'])]
    public ?int $assigned_to = null;

    // Guest details
    #[Rule(['nullable', 'string', 'max:255'])]
    public ?string $name = null;

    #[Rule(['nullable', 'string', 'max:50'])]
    public ?string $room_number = null;

    #[Rule(['required', 'filled', 'date'])]
    public string $checkin_date = '';

    #[Rule(['required', 'filled', 'date'])]
    public string $checkout_date = '';

    #[Rule(['required', 'filled', 'date'])]
    public string $issue_date = '';

    #[Rule(['nullable', 'string', 'max:255'])]
    public ?string $source = null;

    #[Rule(['nullable', 'string', 'max:100'])]
    public ?string $nationality = null;

    #[Rule(['nullable', 'string', 'max:100'])]
    public ?string $contact = null;

    #[Rule(['nullable', 'string'])]
    public ?string $recovery = null;

    #[Rule(['nullable', 'integer', 'min:0'])]
    public ?int $recovery_cost = null;

    #[Rule(['nullable', 'string'])]
    public ?string $training = null;

    public bool $isEditing = false;

    // AI-related properties
    public bool $aiLoading = false;

    protected IssueService $issueService;

    protected IssueAIService $aiService;

    public function boot(IssueService $issueService, IssueAIService $aiService): void
    {
        $this->issueService = $issueService;
        $this->aiService = $aiService;
    }

    public function mount(?Issue $issue = null): void
    {
        if ($issue) {
            $this->issue = $issue;
            $this->isEditing = true;
            $this->title = $issue->title;
            $this->description = $issue->description;
            $this->priority = $issue->priority;
            $this->location = $issue->location;
            $this->assigned_to = $issue->assigned_to_user_id;
            $this->department_ids = $issue->departments->pluck('id')->toArray();
            $this->issue_type_ids = $issue->issueTypes->pluck('id')->toArray();

            // Guest details
            $this->name = $issue->name;
            $this->room_number = $issue->room_number;
            $this->checkin_date = $issue->checkin_date?->format('Y-m-d') ?? '';
            $this->checkout_date = $issue->checkout_date?->format('Y-m-d') ?? '';
            $this->issue_date = $issue->issue_date?->format('Y-m-d') ?? '';
            $this->source = $issue->source;
            $this->nationality = $issue->nationality;
            $this->contact = $issue->contact;
            $this->recovery = $issue->recovery;
            $this->recovery_cost = $issue->recovery_cost;
            $this->training = $issue->training;

            $this->authorize('update', $issue);
        } else {
            $this->authorize('create', Issue::class);
        }
    }

    /**
     * Applies check-in and check-out from the range picker in one request so Livewire does not
     * re-render after the first date tap (which was resetting Alpine and breaking range selection).
     */
    public function syncCheckinCheckoutDates(string $checkin, string $checkout): void
    {
        $this->checkin_date = $checkin;
        $this->checkout_date = $checkout;
    }

    public function save()
    {
        $this->validate([
            'checkin_date' => ['required', 'filled', 'date'],
            'checkout_date' => ['required', 'filled', 'date'],
            'issue_date' => ['required', 'filled', 'date'],
            'department_ids' => ['required', 'array', 'min:1'],
            'department_ids.*' => ['exists:departments,id'],
            'issue_type_ids' => ['required', 'array', 'min:1'],
            'issue_type_ids.*' => ['exists:issue_types,id'],
        ]);

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'location' => $this->location,
            'assigned_to_user_id' => $this->assigned_to ?: null,
            'department_ids' => $this->department_ids,
            'issue_type_ids' => $this->issue_type_ids,
            // Guest details
            'name' => $this->name,
            'room_number' => $this->room_number,
            'checkin_date' => $this->checkin_date,
            'checkout_date' => $this->checkout_date,
            'issue_date' => $this->issue_date,
            'source' => $this->source,
            'nationality' => $this->nationality,
            'contact' => $this->contact,
            'recovery' => $this->recovery,
            'recovery_cost' => $this->recovery_cost,
            'training' => $this->training,
        ];

        if ($this->isEditing) {
            $issue = $this->issueService->update($this->issue, $data);
            session()->flash('success', 'Issue updated successfully.');
            $this->dispatch('issue-updated');
        } else {
            $data['created_by'] = auth()->id();
            $issue = $this->issueService->create($data);
            session()->flash('success', 'Issue created successfully.');
            $this->dispatch('issue-created');
        }

        return $this->redirectRoute('issues.show', $issue);
    }

    public function render()
    {
        return view('livewire.issues.form')
            ->layout('layouts.app')
            ->title($this->isEditing ? 'Edit Issue' : 'Create Issue');
    }

    public function cancel()
    {
        if ($this->isEditing) {
            return $this->redirectRoute('issues.show', $this->issue);
        }

        return $this->redirectRoute('issues.index');
    }

    public function assistWithAI(): void
    {
        // Validate description length
        if (empty($this->description) || strlen($this->description) < 10) {
            session()->flash('ai_error', '⚠️ Please enter at least 10 characters in the description before using AI enhancement.');

            return;
        }

        $this->aiLoading = true;

        try {
            // Store original values for rollback
            $originalDescription = $this->description;
            $originalIssueTypes = $this->issue_type_ids;
            $originalDepartments = $this->department_ids;

            // Call AI service
            $result = $this->aiService->analyzeAndEnhanceIssue(
                $this->description ?? '',
                $this->title
            );

            // Update form fields
            $this->description = $result['enhanced_description'];
            $this->issue_type_ids = $result['issue_type_ids'];
            $this->department_ids = $result['department_ids'];

            // Clear failure state
            session()->forget(['ai_failure_time', 'ai_error']);

            session()->flash('ai_success', '✨ Enhanced with AI!');

        } catch (AIException $e) {
            // Rollback to original values
            $this->description = $originalDescription ?? null;
            $this->issue_type_ids = $originalIssueTypes ?? [];
            $this->department_ids = $originalDepartments ?? [];

            // Mark AI as unavailable
            session()->put('ai_failure_time', now());
            session()->put('ai_error', $e->getType()->value);

            session()->flash('ai_error', '⚠️ '.$e->getUserMessage());

            // Log for admin
            \Log::warning('AI Enhancement Failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'type' => $e->getType()->value,
            ]);

        } finally {
            $this->aiLoading = false;
        }
    }

    public function retryAI(): void
    {
        session()->forget(['ai_failure_time', 'ai_error']);
        $this->assistWithAI();
    }

    public function getAiAvailableProperty(): bool
    {
        // Check if AI feature is enabled in config
        if (! config('services.ai.enabled', true)) {
            \Log::info('AI feature disabled in config');

            return false;
        }

        // Check if API key is configured
        if (empty(config('services.deepseek.api_key'))) {
            \Log::warning('DeepSeek API key not configured');

            return false;
        }

        $lastFailure = session()->get('ai_failure_time');
        if (! $lastFailure) {
            return true;
        }

        $cooldownMinutes = config('services.ai.failure_cooldown_minutes', 5);
        $minutesSinceFailure = now()->diffInMinutes($lastFailure);

        $available = $minutesSinceFailure > $cooldownMinutes;

        // Auto-clear if cooldown passed
        if ($available) {
            session()->forget(['ai_failure_time', 'ai_error']);
        }

        return $available;
    }

    public function getAiErrorProperty(): ?string
    {
        return session()->get('ai_error');
    }

    public function getAiRetryAfterProperty(): ?string
    {
        $lastFailure = session()->get('ai_failure_time');
        if (! $lastFailure) {
            return null;
        }

        $cooldownMinutes = config('services.ai.failure_cooldown_minutes', 5);
        $retryAt = $lastFailure->addMinutes($cooldownMinutes);

        if (now()->gte($retryAt)) {
            session()->forget('ai_failure_time');

            return null;
        }

        return $retryAt->diffForHumans();
    }

    public function getDepartmentsProperty(): array
    {
        return Department::orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getIssueTypesProperty(): array
    {
        return IssueType::orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function getUsersProperty(): array
    {
        return User::orderBy('name')
            ->where('is_active', true)
            ->get()
            ->mapWithKeys(fn ($user) => [$user->id => $user->name])
            ->toArray();
    }

    public function getPrioritiesProperty(): array
    {
        return [
            'urgent' => 'Urgent',
            'high' => 'High',
            'medium' => 'Medium',
            'low' => 'Low',
        ];
    }

    public function getPriorityColorsProperty(): array
    {
        return [
            'urgent' => 'text-danger bg-danger/20',
            'high' => 'text-warning bg-warning/20',
            'medium' => 'text-muted bg-muted/20',
            'low' => 'text-muted bg-muted/20',
        ];
    }

    public function getNationalitiesProperty(): array
    {
        return [
            'Afghan', 'Albanian', 'Algerian', 'American', 'Andorran', 'Angolan', 'Argentine', 'Armenian',
            'Australian', 'Austrian', 'Azerbaijani', 'Bahamian', 'Bahraini', 'Bangladeshi', 'Barbadian',
            'Belarusian', 'Belgian', 'Belizean', 'Beninese', 'Bhutanese', 'Bolivian', 'Bosnian',
            'Botswanan', 'Brazilian', 'Bruneian', 'Bulgarian', 'Burkinabe', 'Burmese', 'Burundian',
            'Cambodian', 'Cameroonian', 'Canadian', 'Cape Verdean', 'Central African', 'Chadian',
            'Chilean', 'Chinese', 'Colombian', 'Comoran', 'Congolese', 'Costa Rican', 'Croatian',
            'Cuban', 'Cypriot', 'Czech', 'Danish', 'Djiboutian', 'Dominican', 'Dutch', 'East Timorese',
            'Ecuadorian', 'Egyptian', 'Emirati', 'English', 'Eritrean', 'Estonian', 'Ethiopian',
            'Fijian', 'Filipino', 'Finnish', 'French', 'Gabonese', 'Gambian', 'Georgian', 'German',
            'Ghanaian', 'Greek', 'Grenadian', 'Guatemalan', 'Guinean', 'Guyanese', 'Haitian', 'Honduran',
            'Hong Konger', 'Hungarian', 'Icelandic', 'Indian', 'Indonesian', 'Iranian', 'Iraqi', 'Irish',
            'Israeli', 'Italian', 'Ivorian', 'Jamaican', 'Japanese', 'Jordanian', 'Kazakh', 'Kenyan',
            'Kiribati', 'Kosovan', 'Kuwaiti', 'Kyrgyz', 'Laotian', 'Latvian', 'Lebanese', 'Liberian',
            'Libyan', 'Liechtensteiner', 'Lithuanian', 'Luxembourger', 'Macedonian', 'Malagasy',
            'Malawian', 'Malaysian', 'Maldivian', 'Malian', 'Maltese', 'Marshallese', 'Mauritanian',
            'Mauritian', 'Mexican', 'Micronesian', 'Moldovan', 'Monegasque', 'Mongolian', 'Montenegrin',
            'Moroccan', 'Mozambican', 'Namibian', 'Nauruan', 'Nepalese', 'New Zealander', 'Nicaraguan',
            'Nigerian', 'Nigerien', 'North Korean', 'Northern Irish', 'Norwegian', 'Omani', 'Pakistani',
            'Palauan', 'Palestinian', 'Panamanian', 'Papua New Guinean', 'Paraguayan', 'Peruvian', 'Polish',
            'Portuguese', 'Qatari', 'Romanian', 'Russian', 'Rwandan', 'Saint Lucian', 'Salvadoran',
            'Samoan', 'San Marinese', 'Sao Tomean', 'Saudi', 'Scottish', 'Senegalese', 'Serbian',
            'Seychellois', 'Sierra Leonean', 'Singaporean', 'Slovak', 'Slovenian', 'Solomon Islander',
            'Somali', 'South African', 'South Korean', 'South Sudanese', 'Spanish', 'Sri Lankan',
            'Sudanese', 'Surinamese', 'Swazi', 'Swedish', 'Swiss', 'Syrian', 'Taiwanese', 'Tajik',
            'Tanzanian', 'Thai', 'Togolese', 'Tongan', 'Trinidadian', 'Tunisian', 'Turkish', 'Turkmen',
            'Tuvaluan', 'Ugandan', 'Ukrainian', 'Uruguayan', 'Uzbek', 'Vanuatuan', 'Vatican', 'Venezuelan',
            'Vietnamese', 'Welsh', 'Yemeni', 'Zambian', 'Zimbabwean',
        ];
    }
}
