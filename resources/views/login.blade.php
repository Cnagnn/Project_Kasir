@extends('layouts.auth')

@section('content')

<div class="auth-form-light text-left py-5 px-4 px-sm-5">
    <div class="brand-logo text-center mb-4">
        <img src="{{ asset('admin/images/logo.svg') }}" alt="logo">
    </div>
    
    {{-- Display Session Messages --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <form class="pt-3" method="POST" action="{{ route('login.process') }}" id="loginForm" novalidate>
        @csrf
        
        {{-- Email Field --}}
        <div class="form-group">
            <label for="email" class="form-label">Alamat Email</label>
            <input type="email" 
                   class="form-control form-control-lg @error('email') is-invalid @enderror" 
                   id="email" 
                   name="email" 
                   placeholder="Enter your email"
                   value="{{ old('email') }}"
                   required
                   autocomplete="email"
                   autofocus>
            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
        
        {{-- Password Field --}}
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <input type="password" 
                       class="form-control form-control-lg @error('password') is-invalid @enderror" 
                       id="password" 
                       name="password" 
                       placeholder="Enter your password"
                       required
                       autocomplete="current-password"
                       style="border-right: none;">
                <span class="input-group-text bg-transparent" style="border-left: none; cursor: pointer;" id="togglePassword">
                    <i class="mdi mdi-eye-outline" id="toggleIcon" style="font-size: 1.25rem; color: #6c757d;"></i>
                </span>
                @error('password')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        
        {{-- Submit Button --}}
        <div class="mt-3 d-grid gap-2">
            <button type="submit" class="btn btn-block btn-primary btn-lg fw-medium auth-form-btn" id="submitBtn">
                Masuk
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Gunakan vanilla JavaScript untuk memastikan kompatibilitas
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    // Toggle password visibility
    if (togglePassword && passwordField && toggleIcon) {
        togglePassword.addEventListener('click', function() {
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('mdi-eye-outline');
                toggleIcon.classList.add('mdi-eye-off-outline');
                toggleIcon.style.color = '#1F3BB3';
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('mdi-eye-off-outline');
                toggleIcon.classList.add('mdi-eye-outline');
                toggleIcon.style.color = '#6c757d';
            }
        });
        
        // Hover effect
        togglePassword.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        
        togglePassword.addEventListener('mouseleave', function() {
            this.style.backgroundColor = 'transparent';
        });
    }
});

// jQuery version (jika jQuery sudah dimuat)
$(document).ready(function() {
    // Form validation
    $('#loginForm').on('submit', function(e) {
        let isValid = true;
        const email = $('#email').val().trim();
        const password = $('#password').val();
        
        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        
        // Email validation
        if (email === '') {
            $('#email').addClass('is-invalid');
            $('#email').after('<div class="invalid-feedback">Email is required.</div>');
            isValid = false;
        } else if (!isValidEmail(email)) {
            $('#email').addClass('is-invalid');
            $('#email').after('<div class="invalid-feedback">Please enter a valid email address.</div>');
            isValid = false;
        }
        
        // Password validation
        if (password === '') {
            $('#password').addClass('is-invalid');
            $('#password').parent().after('<div class="invalid-feedback d-block">Password is required.</div>');
            isValid = false;
        } else if (password.length < 6) {
            $('#password').addClass('is-invalid');
            $('#password').parent().after('<div class="invalid-feedback d-block">Password must be at least 6 characters.</div>');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        const submitBtn = $('#submitBtn');
        submitBtn.prop('disabled', true);
        submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Signing in...');
    });
    
    // Email validation helper
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush

@endsection