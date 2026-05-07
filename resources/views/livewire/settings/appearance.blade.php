<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Appearance')" :subheading=" __('Update the appearance settings for your account')">
        <fieldset class="theme-choice-list" aria-label="{{ __('Theme') }}">
            <label class="theme-choice">
                <input type="radio" name="theme" value="crt" data-theme-choice="crt">
                <span>{{ __('CRT') }}</span>
            </label>

            <label class="theme-choice">
                <input type="radio" name="theme" value="amber" data-theme-choice="amber">
                <span>{{ __('Amber') }}</span>
            </label>

            <label class="theme-choice">
                <input type="radio" name="theme" value="paper" data-theme-choice="paper">
                <span>{{ __('Paper') }}</span>
            </label>
        </fieldset>
    </x-settings.layout>
</section>
