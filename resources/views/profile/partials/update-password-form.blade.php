<section class="space-y-6">
    

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <ul class="list-group">
            <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                <div class="d-flex flex-column">
                    <h6 class="mb-3 text-sm">{{ Auth::user()->name }}</h6>
                    <span class="mb-2 text-xs">Current Password: <span class="text-dark font-weight-bold ms-sm-2">********</span></span>
                    <span class="mb-2 text-xs">New Password: <span class="text-dark font-weight-bold ms-sm-2">********</span></span>
                    <span class="text-xs">Confirm Password: <span class="text-dark font-weight-bold ms-sm-2">********</span></span>
                </div>
                <div class="ms-auto text-end">
                    <a class="btn btn-link text-dark px-3 mb-0" href="javascript:;" data-bs-toggle="modal" data-bs-target="#editPasswordModal"><i class="material-symbols-rounded text-sm me-2">edit</i>Edit</a>
                </div>
            </li>
        </ul>

        @if (session('status') === 'password-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-gray-600 dark:text-gray-400"
            >{{ __('Saved.') }}</p>
        @endif
    </form>
</section>

<!-- Edit Password Modal -->
<div class="modal fade" id="editPasswordModal" tabindex="-1" aria-labelledby="editPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPasswordModalLabel">Edit Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" autocomplete="current-password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" autocomplete="new-password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>