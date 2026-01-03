<x-layout title="Thoughts">
    <div class="stack thoughts">
        <section class="section thoughts-composer" aria-label="New Thought">
            <header class="section__header">
                <h1 class="section__title">
                    <x-heroicon-o-pencil-square class="section__title-icon" aria-hidden="true" focusable="false"
                        width="24" height="24" />
                    Share a Thought
                </h1>
            </header>
            <div class="section__content">
                <x-form action="#" method="POST" enc-type="multipart/form-data">
                    <div class="form-field">
                        <textarea class="form-textarea" id="content" name="content" placeholder="What’s on your mind?" required></textarea>
                    </div>
                    <div class="thoughts-composer-row">
                        <x-visibility-radio :selected="(string) \App\Visibility::PRIVATE->value" />
                        <div class="thoughts-attach">
                            <label class="btn" for="attachment">
                                <x-heroicon-o-paper-clip class="btn__icon" aria-hidden="true" focusable="false"
                                    width="16" height="16" />
                            </label>
                            <input class="thoughts-attach__input" id="attachment" name="attachment" type="file" />
                        </div>
                    </div>
                    <button class="btn btn--primary" type="submit">Post</button>
                </x-form>
            </div>
        </section>

        @php
            $defaultVisibility = (string) \App\Visibility::PRIVATE->value;

            $thoughts = [
                [
                    'id' => 1,
                    'created_at' => now()->subMinutes(7),
                    'content' => 'Shipped a tiny UI refactor today. Feels cleaner already.',
                ],
                [
                    'id' => 2,
                    'created_at' => now()->subHours(2),
                    'content' =>
                        "Note to self:\n- Keep controllers thin\n- Move logic into services\n- Write the test first next time",
                ],
                [
                    'id' => 3,
                    'created_at' => now()->subDay(),
                    'content' => 'Coffee is a build dependency.',
                ],
            ];
        @endphp

        <ol class="thoughts-feed" role="list">
            @foreach ($thoughts as $thought)
                <li class="thoughts-item">
                    <header class="thoughts-item__header">
                        <time class="thoughts-item__time" datetime="{{ $thought['created_at']->toW3cString() }}">
                            {{ $thought['created_at']->format('M j, Y g:i A') }}
                        </time>

                        <details class="thoughts-actions">
                            <summary class="thoughts-actions__trigger" aria-label="Actions">
                                <x-heroicon-o-ellipsis-horizontal aria-hidden="true" focusable="false" width="18"
                                    height="18" />
                            </summary>

                            <div class="thoughts-actions__menu" role="menu">
                                <a class="thoughts-actions__item" href="#" role="menuitem">Edit</a>
                                <a class="thoughts-actions__item" href="#" role="menuitem">Delete</a>
                            </div>
                        </details>
                    </header>

                    <div class="thoughts-item__content">
                        {!! nl2br(e($thought['content'])) !!}
                    </div>
                </li>
            @endforeach
        </ol>

    </div>
</x-layout>
