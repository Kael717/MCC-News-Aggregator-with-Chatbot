@extends('layouts.auth')

@section('title', 'Complete MS365 Registration - MCC News Aggregator')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        <h1>Complete MS365 Registration</h1>
        <p>MCC News Aggregator with Chatbot</p>
        <p class="subtitle">Complete your institutional account setup</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $error)
                <div><i class="fas fa-exclamation-circle"></i> {{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if(isset($email))
        <div class="email-verified">
            <i class="fas fa-check-circle"></i>
            <span>MS365 verified: <strong>{{ $email }}</strong></span>
        </div>
    @endif

    <form method="POST" action="{{ route('ms365.register.complete') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="ms365_account" value="{{ $email ?? '' }}">

        <div class="form-group">
            <label for="ms365_account">
                <i class="fab fa-microsoft"></i>
                MS365 Account
            </label>
            <input type="email"
                   id="ms365_account"
                   name="ms365_account"
                   class="form-control"
                   value="{{ $email ?? '' }}"
                   readonly
                   required>
            <small class="form-help">
                <i class="fas fa-shield-alt"></i>
                This MS365 address was verified through your registration link
            </small>
        </div>

        <div class="form-group">
            <label for="first_name">
                <i class="fas fa-user"></i>
                First Name
            </label>
            <input type="text"
                   id="first_name"
                   name="first_name"
                   class="form-control @error('first_name') error @enderror"
                   value="{{ old('first_name') }}"
                   placeholder="Enter first name"
                   pattern="[A-Za-z' ]+"
                   title="Only letters and single quotation marks are allowed"
                   required>
            @error('first_name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="middle_name">
                <i class="fas fa-user"></i>
                Middle Name
            </label>
            <input type="text"
                   id="middle_name"
                   name="middle_name"
                   class="form-control @error('middle_name') error @enderror"
                   value="{{ old('middle_name') }}"
                   placeholder="Enter middle name (optional)"
                   pattern="[A-Za-z' ]+"
                   title="Only letters and single quotation marks are allowed">
            @error('middle_name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="surname">
                <i class="fas fa-user"></i>
                Surname
            </label>
            <input type="text"
                   id="surname"
                   name="surname"
                   class="form-control @error('surname') error @enderror"
                   value="{{ old('surname') }}"
                   placeholder="Enter surname"
                   pattern="[A-Za-z' ]+"
                   title="Only letters and single quotation marks are allowed"
                   required>
            @error('surname')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="role">
                <i class="fas fa-user-tag"></i>
                Role
            </label>
            <select id="role"
                    name="role"
                    class="form-control @error('role') error @enderror"
                    onchange="toggleDepartmentFields()"
                    required>
                <option value="">Select Role</option>
                <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                <option value="faculty" {{ old('role') == 'faculty' ? 'selected' : '' }}>Faculty</option>
            </select>
            @error('role')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group department-field" id="department-field">
            <label for="department">
                <i class="fas fa-building"></i>
                Department
            </label>
            <select id="department"
                    name="department"
                    class="form-control @error('department') error @enderror">
                <option value="">Select Department</option>
                <option value="Bachelor of Science in Information Technology" {{ old('department') == 'Bachelor of Science in Information Technology' ? 'selected' : '' }}>Bachelor of Science in Information Technology</option>
                <option value="Bachelor of Science in Business Administration" {{ old('department') == 'Bachelor of Science in Business Administration' ? 'selected' : '' }}>Bachelor of Science in Business Administration</option>
                <option value="Bachelor of Elementary Education" {{ old('department') == 'Bachelor of Elementary Education' ? 'selected' : '' }}>Bachelor of Elementary Education</option>
                <option value="Bachelor of Secondary Education" {{ old('department') == 'Bachelor of Secondary Education' ? 'selected' : '' }}>Bachelor of Secondary Education</option>
                <option value="Bachelor of Science in Hospitality Management" {{ old('department') == 'Bachelor of Science in Hospitality Management' ? 'selected' : '' }}>Bachelor of Science in Hospitality Management</option>
            </select>
            @error('department')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group year-level-field" id="year-level-field" style="display: none;">
            <label for="year_level">
                <i class="fas fa-graduation-cap"></i>
                Year Level
            </label>
            <select id="year_level"
                    name="year_level"
                    class="form-control @error('year_level') error @enderror">
                <option value="">Select Year Level</option>
                <option value="1st Year" {{ old('year_level') == '1st Year' ? 'selected' : '' }}>1st Year</option>
                <option value="2nd Year" {{ old('year_level') == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                <option value="3rd Year" {{ old('year_level') == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                <option value="4th Year" {{ old('year_level') == '4th Year' ? 'selected' : '' }}>4th Year</option>
            </select>
            @error('year_level')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">
                <i class="fas fa-lock"></i>
                Password
            </label>
            <div class="password-input-container">
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control @error('password') error @enderror"
                       placeholder="Create a secure password"
                       minlength="8"
                       required>
                <span class="toggle-password" onclick="togglePassword('password')">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
            @error('password')
                <div class="error-message">{{ $message }}</div>
            @enderror
            <small class="form-help">
                <i class="fas fa-info-circle"></i>
                Password must be at least 8 characters long
            </small>
        </div>

        <div class="form-group">
            <label for="password_confirmation">
                <i class="fas fa-lock"></i>
                Confirm Password
            </label>
            <div class="password-input-container">
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       class="form-control @error('password_confirmation') error @enderror"
                       placeholder="Confirm your password"
                       minlength="8"
                       required>
                <span class="toggle-password" onclick="togglePassword('password_confirmation')">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
            @error('password_confirmation')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- reCAPTCHA -->
        <div class="form-group">
            <label for="recaptcha">
                <i class="fas fa-shield-alt"></i>
                Security Verification
            </label>
            <div class="recaptcha-container">
                {!! NoCaptcha::display() !!}
            </div>
            @error('g-recaptcha-response')
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fab fa-microsoft"></i>
            Complete MS365 Registration
        </button>
    </form>

    <div class="auth-links">
        <a href="{{ route('login') }}">
            <i class="fas fa-arrow-left"></i>
            Already have an account? Login
        </a>
    </div>
</div>

<script>
    function togglePassword(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const icon = document.querySelector(`#${fieldId} + .toggle-password i`);

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Toggle department and year level fields based on role
    function toggleDepartmentFields() {
        const roleSelect = document.getElementById('role');
        const departmentField = document.getElementById('department-field');
        const yearLevelField = document.getElementById('year-level-field');
        const departmentSelect = document.getElementById('department');
        const yearLevelSelect = document.getElementById('year_level');

        if (roleSelect.value === 'student') {
            departmentField.style.display = 'block';
            yearLevelField.style.display = 'block';
            departmentSelect.required = true;
            yearLevelSelect.required = true;
        } else if (roleSelect.value === 'faculty') {
            departmentField.style.display = 'block';
            yearLevelField.style.display = 'none';
            departmentSelect.required = true;
            yearLevelSelect.required = false;
            yearLevelSelect.value = '';
        } else {
            departmentField.style.display = 'none';
            yearLevelField.style.display = 'none';
            departmentSelect.required = false;
            yearLevelSelect.required = false;
            departmentSelect.value = '';
            yearLevelSelect.value = '';
        }
    }

    // Validate name inputs (only letters, spaces, and apostrophes)
    function validateNameInput(event) {
        const allowedChars = /[A-Za-z' ]/;
        if (!allowedChars.test(event.key) && event.key !== 'Backspace' && event.key !== 'Delete') {
            event.preventDefault();
        }
    }

    // Add event listeners
    document.getElementById('first_name').addEventListener('keypress', validateNameInput);
    document.getElementById('middle_name').addEventListener('keypress', validateNameInput);
    document.getElementById('surname').addEventListener('keypress', validateNameInput);

    // Initialize department fields visibility on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleDepartmentFields();
    });
</script>

<script>
    function togglePassword(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const icon = document.querySelector(`#${fieldId} + .toggle-password i`);

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Toggle department and year level fields based on role
    function toggleDepartmentFields() {
        const roleSelect = document.getElementById('role');
        const departmentField = document.getElementById('department-field');
        const yearLevelField = document.getElementById('year-level-field');
        const departmentSelect = document.getElementById('department');
        const yearLevelSelect = document.getElementById('year_level');

        if (roleSelect.value === 'student') {
            departmentField.style.display = 'block';
            yearLevelField.style.display = 'block';
            departmentSelect.required = true;
            yearLevelSelect.required = true;
        } else if (roleSelect.value === 'faculty') {
            departmentField.style.display = 'block';
            yearLevelField.style.display = 'none';
            departmentSelect.required = true;
            yearLevelSelect.required = false;
            yearLevelSelect.value = '';
        } else {
            departmentField.style.display = 'none';
            yearLevelField.style.display = 'none';
            departmentSelect.required = false;
            yearLevelSelect.required = false;
            departmentSelect.value = '';
            yearLevelSelect.value = '';
        }
    }

    // Validate name inputs (only letters, spaces, and apostrophes)
    function validateNameInput(event) {
        const allowedChars = /[A-Za-z' ]/;
        if (!allowedChars.test(event.key) && event.key !== 'Backspace' && event.key !== 'Delete') {
            event.preventDefault();
        }
    }

    // Add event listeners
    document.getElementById('first_name').addEventListener('keypress', validateNameInput);
    document.getElementById('middle_name').addEventListener('keypress', validateNameInput);
    document.getElementById('surname').addEventListener('keypress', validateNameInput);

    // Initialize department fields visibility on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleDepartmentFields();
    });
</script>

<style>
    .subtitle {
        color: #666;
        font-size: 14px;
        margin-top: 5px;
    }

    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .email-verified {
        background: #d1ecf1;
        color: #0c5460;
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        border-left: 4px solid #17a2b8;
    }

    .form-group label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        margin-bottom: 8px;
    }

    .form-help {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 6px;
        font-size: 12px;
        color: #666;
    }

    .password-input-container {
        position: relative;
    }

    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #666;
        z-index: 10;
    }

    .toggle-password:hover {
        color: #333;
    }

    .form-control {
        padding-right: 35px;
    }

    .form-control[readonly] {
        background-color: #f8f9fa;
        border-color: #e9ecef;
        color: #6c757d;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0078d4, #005a9e);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 500;
        width: 100%;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #106ebe, #004578);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 120, 212, 0.3);
    }

    .auth-links a {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #666;
        text-decoration: none;
        font-size: 14px;
        transition: color 0.3s ease;
    }

    .auth-links a:hover {
        color: #0078d4;
    }

    .fab.fa-microsoft {
        color: #0078d4;
    }

    .fas.fa-shield-alt {
        color: #28a745;
    }

    .form-control:focus {
        border-color: #0078d4;
        box-shadow: 0 0 0 2px rgba(0, 120, 212, 0.2);
    }

    /* Department and Year Level Fields */
    .department-field, .year-level-field {
        transition: all 0.3s ease;
    }

    .department-field.show, .year-level-field.show {
        display: block !important;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Icon colors */
    .fas.fa-user-tag {
        color: #6f42c1;
    }

    .fas.fa-building {
        color: #fd7e14;
    }

    .fas.fa-graduation-cap {
        color: #20c997;
    }

    /* Form validation styling */
    .form-control.error {
        border-color: #dc3545;
        box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.2);
    }

    .error-message {
        color: #dc3545;
        font-size: 12px;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .error-message::before {
        content: "⚠️";
        font-size: 10px;
    }
</style>






<script>

    function togglePassword(fieldId) {

        const passwordField = document.getElementById(fieldId);

        const icon = document.querySelector(`#${fieldId} + .toggle-password i`);



        if (passwordField.type === 'password') {

            passwordField.type = 'text';

            icon.classList.remove('fa-eye');

            icon.classList.add('fa-eye-slash');

        } else {

            passwordField.type = 'password';

            icon.classList.remove('fa-eye-slash');

            icon.classList.add('fa-eye');

        }

    }



    // Toggle department and year level fields based on role

    function toggleDepartmentFields() {

        const roleSelect = document.getElementById('role');

        const departmentField = document.getElementById('department-field');

        const yearLevelField = document.getElementById('year-level-field');

        const departmentSelect = document.getElementById('department');

        const yearLevelSelect = document.getElementById('year_level');



        if (roleSelect.value === 'student') {

            departmentField.style.display = 'block';

            yearLevelField.style.display = 'block';

            departmentSelect.required = true;

            yearLevelSelect.required = true;

        } else if (roleSelect.value === 'faculty') {

            departmentField.style.display = 'block';

            yearLevelField.style.display = 'none';

            departmentSelect.required = true;

            yearLevelSelect.required = false;

            yearLevelSelect.value = '';

        } else {

            departmentField.style.display = 'none';

            yearLevelField.style.display = 'none';

            departmentSelect.required = false;

            yearLevelSelect.required = false;

            departmentSelect.value = '';

            yearLevelSelect.value = '';

        }

    }



    // Validate name inputs (only letters, spaces, and apostrophes)

    function validateNameInput(event) {

        const allowedChars = /[A-Za-z' ]/;

        if (!allowedChars.test(event.key) && event.key !== 'Backspace' && event.key !== 'Delete') {

            event.preventDefault();

        }

    }



    // Add event listeners

    document.getElementById('first_name').addEventListener('keypress', validateNameInput);

    document.getElementById('middle_name').addEventListener('keypress', validateNameInput);

    document.getElementById('surname').addEventListener('keypress', validateNameInput);



    // Initialize department fields visibility on page load

    document.addEventListener('DOMContentLoaded', function() {

        toggleDepartmentFields();

    });

</script>



<style>

    .subtitle {

        color: #666;

        font-size: 14px;

        margin-top: 5px;

    }



    .alert {

        padding: 15px 20px;

        border-radius: 8px;

        margin-bottom: 20px;

        font-size: 14px;

        display: flex;

        align-items: center;

        gap: 10px;

    }



    .alert-success {

        background: #d4edda;

        color: #155724;

        border: 1px solid #c3e6cb;

    }



    .alert-error {

        background: #f8d7da;

        color: #721c24;

        border: 1px solid #f5c6cb;

    }



    .email-verified {

        background: #d1ecf1;

        color: #0c5460;

        padding: 12px 16px;

        border-radius: 6px;

        margin-bottom: 20px;

        display: flex;

        align-items: center;

        gap: 8px;

        font-size: 14px;

        border-left: 4px solid #17a2b8;

    }



    .form-group label {

        display: flex;

        align-items: center;

        gap: 8px;

        font-weight: 500;

        margin-bottom: 8px;

    }



    .form-help {

        display: flex;

        align-items: center;

        gap: 6px;

        margin-top: 6px;

        font-size: 12px;

        color: #666;

    }



    .password-input-container {

        position: relative;

    }



    .toggle-password {

        position: absolute;

        right: 10px;

        top: 50%;

        transform: translateY(-50%);

        cursor: pointer;

        color: #666;

        z-index: 10;

    }



    .toggle-password:hover {

        color: #333;

    }



    .form-control {

        padding-right: 35px;

    }



    .form-control[readonly] {

        background-color: #f8f9fa;

        border-color: #e9ecef;

        color: #6c757d;

    }



    .btn-primary {

        background: linear-gradient(135deg, #0078d4, #005a9e);

        color: white;

        border: none;

        padding: 12px 20px;

        border-radius: 6px;

        font-size: 16px;

        font-weight: 500;

        width: 100%;

        cursor: pointer;

        transition: all 0.3s ease;

        display: flex;

        align-items: center;

        justify-content: center;

        gap: 8px;

    }



    .btn-primary:hover {

        background: linear-gradient(135deg, #106ebe, #004578);

        transform: translateY(-1px);

        box-shadow: 0 4px 12px rgba(0, 120, 212, 0.3);

    }



    .auth-links a {

        display: flex;

        align-items: center;

        gap: 8px;

        color: #666;

        text-decoration: none;

        font-size: 14px;

        transition: color 0.3s ease;

    }



    .auth-links a:hover {

        color: #0078d4;

    }



    .fab.fa-microsoft {

        color: #0078d4;

    }



    .fas.fa-shield-alt {

        color: #28a745;

    }



    .form-control:focus {

        border-color: #0078d4;

        box-shadow: 0 0 0 2px rgba(0, 120, 212, 0.2);

    }



    /* Department and Year Level Fields */

    .department-field, .year-level-field {

        transition: all 0.3s ease;

    }



    .department-field.show, .year-level-field.show {

        display: block !important;

        animation: slideDown 0.3s ease;

    }



    @keyframes slideDown {

        from {

            opacity: 0;

            transform: translateY(-10px);

        }

        to {

            opacity: 1;

            transform: translateY(0);

        }

    }



    /* Icon colors */

    .fas.fa-user-tag {

        color: #6f42c1;

    }



    .fas.fa-building {

        color: #fd7e14;

    }



    .fas.fa-graduation-cap {

        color: #20c997;

    }



    /* Form validation styling */

    .form-control.error {

        border-color: #dc3545;

        box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.2);

    }



    .error-message {

        color: #dc3545;

        font-size: 12px;

        margin-top: 5px;

        display: flex;

        align-items: center;

        gap: 5px;

    }



    .error-message::before {

        content: "⚠️";

        font-size: 10px;

    }

</style>

<!-- reCAPTCHA JavaScript -->
{!! NoCaptcha::renderJs() !!}

@endsection
