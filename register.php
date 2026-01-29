<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
// Show registration errors if redirected back
$registration_errors = isset($_SESSION['registration_errors']) ? $_SESSION['registration_errors'] : [];
unset($_SESSION['registration_errors']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="CSS/styles.css">
</head>

<body>
    <div class="form-box active" id="registration-form">
        <?php if (!empty($registration_errors)): ?>
            <div style="color: red; margin-bottom: 15px;">
                <ul>
                    <?php foreach ($registration_errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="process_register.php" method="post">
            <h2>Register</h2>
            <input type="text" name="LastName" placeholder="Last Name" required>
            <input type="text" name="FisrtName" placeholder="Fisrt Name" required>
            <input type="text" name="MiddleName" placeholder="Middle Name" required>
            <input type="date" id="borthdate" name="birthdate">

            <div class="form-group gender-group">
                <p class="label">Please select your Gender:</p>
                <div class="radio-group">
                    <label class="radio-item"><input type="radio" name="gender" value="male"> Male</label>
                    <label class="radio-item"><input type="radio" name="female" value="female"> Female</label>
                    <label class="radio-item"><input type="radio" name="gender" value="other"> Other</label>
                </div>
            </div>

            <div class="form-group phone-group">
                <label class="label">Phone</label>
                <div class="phone-input">
                    <span class="country-code">+63</span>
                    <input type="tel" name="phone" pattern="[0-9]{10}" placeholder="9171234567" required>
                </div>
            </div>

            <input type="email" name="email" placeholder="Email" required>
            <button type="button" id="nextBtn">Next</button>
            <p>Already have an Account? <a href="index.php">Log in here</a></p>
        </form>
    </div>

    <!-- Password Modal -->
    <div id="passwordModal" class="modal"
        style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
        <div style="background:#fff; padding:30px; border-radius:8px; min-width:300px; position:relative;">
            <h3>Set Your Password</h3>
            <form id="passwordForm" action="process_register.php" method="post">
                <!-- Hidden fields to carry over previous form data -->
                <input type="hidden" name="LastName">
                <input type="hidden" name="FirstName">
                <input type="hidden" name="MiddleName">
                <input type="hidden" name="birthdate">
                <input type="hidden" name="gender">
                <input type="hidden" name="phone">
                <input type="hidden" name="email">
                <input type="password" id="password" name="EnterPassword" placeholder="Enter password" required>
                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm password"
                    required>
                <button type="submit" name="register">Register</button>
                <button type="button" id="closeModal" style="margin-left:10px;">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        // Show modal and transfer form data
        document.getElementById('nextBtn').onclick = function (e) {
            e.preventDefault();
            // Get values from main form
            var form = document.querySelector('#registration-form form');
            var modal = document.getElementById('passwordModal');
            // Transfer values to hidden fields in modal form
            ['LastName', 'FirstName', 'MiddleName', 'birthdate', 'gender', 'phone', 'email'].forEach(function (name) {
                var value = '';
                if (name === 'gender') {
                    var genderInput = form.querySelector('input[name="gender"]:checked');
                    if (genderInput) value = genderInput.value;
                } else {
                    var input = form.querySelector('[name="' + name + '"]');
                    if (input) value = input.value;
                }
                var hidden = modal.querySelector('input[name="' + name + '"]');
                if (hidden) hidden.value = value;
            });
            modal.style.display = 'flex';
        };

        // Close modal
        document.getElementById('closeModal').onclick = function () {
            document.getElementById('passwordModal').style.display = 'none';
        };

        // Optional: Close modal when clicking outside the modal content
        document.getElementById('passwordModal').onclick = function (e) {
            if (e.target === this) this.style.display = 'none';
        };
    </script>
</body>

</html>