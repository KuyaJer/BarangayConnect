@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
{{-- ── Alpine component ──────────────────────────────────────────────────── --}}
<div class="max-w-5xl mx-auto"
     x-data="{
         /* ── avatar ── */
         avatarPreview: '{{ addslashes($profile->avatar_url ?? '') }}',
         avatarChanged: false,

         /* ── change-detection: stringify original vs current ── */
         orig: {
             name:          @js(old('name', $user->name)),
             first_name:    @js(old('first_name',  $profile->first_name  ?? '')),
             middle_name:   @js(old('middle_name', $profile->middle_name ?? '')),
             surname:       @js(old('surname',     $profile->surname     ?? '')),
             suffix:        @js(old('suffix',      $profile->suffix      ?? '')),
             contact_number:@js(old('contact_number', $profile->contact_number ?? '')),
             birthdate:     @js(old('birthdate', $profile->birthdate ? \Carbon\Carbon::parse($profile->birthdate)->format('Y-m-d') : '')),
             gender:        @js(old('gender',        $profile->gender        ?? '')),
             civil_status:  @js(old('civil_status',  $profile->civil_status  ?? '')),
             current_place: @js(old('current_place', $profile->current_place ?? '')),
         },
         cur: {
             name:          @js(old('name', $user->name)),
             first_name:    @js(old('first_name',  $profile->first_name  ?? '')),
             middle_name:   @js(old('middle_name', $profile->middle_name ?? '')),
             surname:       @js(old('surname',     $profile->surname     ?? '')),
             suffix:        @js(old('suffix',      $profile->suffix      ?? '')),
             contact_number:@js(old('contact_number', $profile->contact_number ?? '')),
             birthdate:     @js(old('birthdate', $profile->birthdate ? \Carbon\Carbon::parse($profile->birthdate)->format('Y-m-d') : '')),
             gender:        @js(old('gender',        $profile->gender        ?? '')),
             civil_status:  @js(old('civil_status',  $profile->civil_status  ?? '')),
             current_place: @js(old('current_place', $profile->current_place ?? '')),
         },
         get profileChanged() {
             return this.avatarChanged || JSON.stringify(this.orig) !== JSON.stringify(this.cur);
         },

         /* ── confirm-password modal ── */
         showSaveModal: false,
         modalPw: '',
         modalError: '',
         openSaveModal() {
             if (!this.profileChanged) return;
             this.modalPw    = '';
             this.modalError = '';
             this.showSaveModal = true;
             this.$nextTick(() => this.$refs.modalPwInput?.focus());
         },
         confirmSave() {
             if (!this.modalPw) { this.modalError = 'Please enter your current password.'; return; }
             document.getElementById('confirm_current_password').value = this.modalPw;
             this.showSaveModal = false;
             this.$nextTick(() => document.getElementById('profileForm').submit());
         },

         /* ── password section ── */
         showCurrentPw: false,
         showNewPw:     false,
         showConfirmPw: false,
         pwCurrent: '',
         pwNew:     '',
         pwConfirm: '',
         get pwChanged() { return this.pwCurrent && this.pwNew && this.pwConfirm; },

         /* ── avatar upload with compression ── */
         handleAvatarChange(event) {
             const file = event.target.files[0];
             if (!file) return;
             if (file.size > 10 * 1024 * 1024) {
                 alert('Image is too large. Maximum size is 10 MB.');
                 event.target.value = '';
                 return;
             }
             const reader = new FileReader();
             reader.onload = (e) => {
                 const img = new Image();
                 img.onload = () => {
                     const canvas = document.createElement('canvas');
                     let w = img.width, h = img.height;
                     const maxDim = 400;
                     if (w > maxDim || h > maxDim) {
                         if (w > h) { h = Math.round(h * maxDim / w); w = maxDim; }
                         else       { w = Math.round(w * maxDim / h); h = maxDim; }
                     }
                     canvas.width = w; canvas.height = h;
                     canvas.getContext('2d').drawImage(img, 0, 0, w, h);
                     const compressed = canvas.toDataURL('image/jpeg', 0.75);
                     this.avatarPreview = compressed;
                     this.avatarChanged = true;
                     document.getElementById('avatar_url_input').value = compressed;
                 };
                 img.src = e.target.result;
             };
             reader.readAsDataURL(file);
         }
     }"
     @keydown.escape.window="showSaveModal = false">

{{-- ── Two-column grid: Profile Info | Change Password ────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── Left: Profile Information (wider) ──────────────────────────── --}}
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700">
        <div class="px-6 py-4 border-b dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Profile Information</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Update your personal details and avatar.</p>
        </div>

        <form id="profileForm" method="POST" action="{{ route('profile.update') }}" class="px-6 py-6 space-y-5">
            @csrf
            @method('PUT')
            {{-- Hidden: current password populated by modal --}}
            <input type="hidden" id="confirm_current_password" name="confirm_current_password"/>

            {{-- Avatar --}}
            <div class="flex items-center gap-5">
                <div class="relative flex-shrink-0">
                    <template x-if="avatarPreview">
                        <img :src="avatarPreview" alt="Avatar"
                             class="w-20 h-20 rounded-full object-cover border-2 border-indigo-200 dark:border-indigo-700 shadow"/>
                    </template>
                    <template x-if="!avatarPreview">
                        <div class="w-20 h-20 rounded-full bg-indigo-100 dark:bg-indigo-900/50 border-2 border-indigo-200 dark:border-indigo-700
                                    flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-bold text-2xl shadow">
                            {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(strrchr($user->name, ' ') ?: ' ', 1, 1)) }}
                        </div>
                    </template>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Profile Photo</label>
                    <input type="file" accept="image/*" @change="handleAvatarChange($event)"
                           class="block text-sm text-gray-500 dark:text-gray-400
                                  file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                  file:text-xs file:font-medium
                                  file:bg-indigo-50 file:text-indigo-700
                                  dark:file:bg-indigo-900/30 dark:file:text-indigo-300
                                  hover:file:bg-indigo-100 cursor-pointer"/>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">JPG, PNG or GIF. Max 10 MB.</p>
                    <textarea id="avatar_url_input" name="avatar_url" class="hidden">{{ $profile->avatar_url ?? '' }}</textarea>
                </div>
            </div>

            <div class="border-t dark:border-gray-700 pt-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Display Name --}}
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Display Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name"
                               x-model="cur.name" required
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                      py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                        @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- First Name --}}
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">First Name</label>
                        <input type="text" id="first_name" name="first_name"
                               x-model="cur.first_name"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                      py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                        @error('first_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Middle Name --}}
                    <div>
                        <label for="middle_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Middle Name</label>
                        <input type="text" id="middle_name" name="middle_name"
                               x-model="cur.middle_name"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                      py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                    </div>

                    {{-- Surname --}}
                    <div>
                        <label for="surname" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Surname</label>
                        <input type="text" id="surname" name="surname"
                               x-model="cur.surname"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                      py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                        @error('surname')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Suffix --}}
                    <div>
                        <label for="suffix" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Suffix</label>
                        <input type="text" id="suffix" name="suffix"
                               x-model="cur.suffix"
                               placeholder="Jr., Sr., III, etc."
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                      py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                                      placeholder-gray-400 dark:placeholder-gray-500"/>
                    </div>

                    {{-- Contact Number --}}
                    <div>
                        <label for="contact_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Contact Number</label>
                        <input type="tel" id="contact_number" name="contact_number"
                               x-model="cur.contact_number"
                               placeholder="09XXXXXXXXX"
                               maxlength="11"
                               x-on:input="
                                   let v = $event.target.value.replace(/\D/g,'');
                                   if(v.length>0 && v[0]!=='0') v='0'+v;
                                   if(v.length>1 && v[1]!=='9') v=v[0]+'9'+v.slice(1);
                                   if(v.length>11) v=v.slice(0,11);
                                   $event.target.value=v; cur.contact_number=v;
                               "
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                      py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                                      placeholder-gray-400 dark:placeholder-gray-500"/>
                        <p class="mt-1 text-xs text-gray-400">Must start with 09, exactly 11 digits.</p>
                        @error('contact_number')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Birthdate --}}
                    <div>
                        <label for="birthdate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Birthdate</label>
                        <input type="date" id="birthdate" name="birthdate"
                               x-model="cur.birthdate"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                      py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                    </div>

                    {{-- Gender --}}
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Gender</label>
                        <select id="gender" name="gender" x-model="cur.gender"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                       py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select gender…</option>
                            @foreach(['Male','Female','Non-binary','Prefer not to say'] as $g)
                                <option value="{{ $g }}">{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Civil Status --}}
                    <div>
                        <label for="civil_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Civil Status</label>
                        <select id="civil_status" name="civil_status" x-model="cur.civil_status"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                       bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                       py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select status…</option>
                            @foreach(['Single','Married','Widowed','Separated','Divorced'] as $cs)
                                <option value="{{ $cs }}">{{ $cs }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Current Address --}}
                    <div class="sm:col-span-2">
                        <label for="current_place" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Current Address</label>
                        <input type="text" id="current_place" name="current_place"
                               x-model="cur.current_place"
                               placeholder="Street, Barangay, City/Municipality"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                      py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                                      placeholder-gray-400 dark:placeholder-gray-500"/>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="button"
                        @click="openSaveModal()"
                        :disabled="!profileChanged"
                        :class="profileChanged
                            ? 'bg-indigo-600 hover:bg-indigo-700 text-white cursor-pointer'
                            : 'bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed'"
                        class="px-5 py-2 text-sm font-medium rounded-lg transition-colors shadow-sm">
                    Save Profile
                </button>
            </div>
        </form>
    </div>

    {{-- ── Right: Change Password ───────────────────────────────────────── --}}
    <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-xl shadow-sm border dark:border-gray-700 self-start">
        <div class="px-6 py-4 border-b dark:border-gray-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Change Password</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Use a strong password to keep your account safe.</p>
        </div>

        <form method="POST" action="{{ route('profile.password') }}" class="px-6 py-6 space-y-4">
            @csrf

            {{-- Current Password --}}
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Current Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input :type="showCurrentPw ? 'text' : 'password'"
                           id="current_password" name="current_password"
                           x-model="pwCurrent"
                           autocomplete="current-password"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                  py-2 pl-3 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                    <button type="button" @click="showCurrentPw=!showCurrentPw"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg x-show="!showCurrentPw" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showCurrentPw" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @error('current_password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- New Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    New Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input :type="showNewPw ? 'text' : 'password'"
                           id="password" name="password"
                           x-model="pwNew"
                           autocomplete="new-password"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                  py-2 pl-3 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                    <button type="button" @click="showNewPw=!showNewPw"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg x-show="!showNewPw" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showNewPw" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Minimum 6 characters.</p>
                @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Confirm New Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input :type="showConfirmPw ? 'text' : 'password'"
                           id="password_confirmation" name="password_confirmation"
                           x-model="pwConfirm"
                           autocomplete="new-password"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                                  bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                  py-2 pl-3 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                    <button type="button" @click="showConfirmPw=!showConfirmPw"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg x-show="!showConfirmPw" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showConfirmPw" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @error('password_confirmation')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                        :disabled="!pwChanged"
                        :class="pwChanged
                            ? 'bg-indigo-600 hover:bg-indigo-700 text-white cursor-pointer'
                            : 'bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed'"
                        class="px-5 py-2 text-sm font-medium rounded-lg transition-colors shadow-sm">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Confirm Current Password Modal ──────────────────────────────────── --}}
<div x-show="showSaveModal" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

    <div class="absolute inset-0 bg-black/50" @click="showSaveModal=false"></div>

    <div class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-xl border dark:border-gray-700"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         @click.stop>

        <div class="flex items-center justify-between px-6 py-4 border-b dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Confirm Identity</h3>
            <button @click="showSaveModal=false"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="px-6 py-5 space-y-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Enter your current password to save profile changes.
            </p>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Current Password <span class="text-red-500">*</span>
                </label>
                <input type="password" x-ref="modalPwInput"
                       x-model="modalPw"
                       @keydown.enter="confirmSave()"
                       autocomplete="current-password"
                       class="w-full rounded-lg border border-gray-300 dark:border-gray-600
                              bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                              py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
                <p x-show="modalError" x-text="modalError"
                   class="mt-1 text-xs text-red-500"></p>
                @error('confirm_current_password')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3 pt-1">
                <button type="button" @click="showSaveModal=false"
                        class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600
                               text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="button" @click="confirmSave()"
                        class="px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700
                               text-white transition-colors">
                    Confirm & Save
                </button>
            </div>
        </div>
    </div>
</div>

</div>{{-- end x-data --}}
@endsection
