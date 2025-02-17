<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$conn = new mysqli('127.0.0.1', 'root', 'mypass', 'resume');
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// Debugging: Check if data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract main form fields
    $first_name = trim($_POST['firstname'] ?? '');
    $middle_name = trim($_POST['middlename'] ?? '');
    $last_name = trim($_POST['lastname'] ?? '');
    $designation = trim($_POST['designation'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone_no = trim($_POST['phoneno'] ?? '');
    $summary = trim($_POST['summary'] ?? '');

    // Get JSON data for repeater fields
    $achievements = $_POST['achievements'] ?? '[]';
    $experiences = $_POST['experiences'] ?? '[]';
    $educations = $_POST['educations'] ?? '[]';
    $projects = $_POST['projects'] ?? '[]';
    $skills = $_POST['skills'] ?? '[]';

    if (empty($first_name) || empty($last_name) || empty($email)) {
        die(json_encode(["error" => "First Name, Last Name, and Email are required."]));
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die(json_encode(["error" => "Invalid email format."]));
    }

    // Handle image upload
    $image_path = '';
    if (!empty($_FILES['image']['name'])) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $image_name = basename($_FILES['image']['name']);
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_path = $upload_dir . time() . '_' . $image_name; // Add timestamp to avoid filename conflicts

        // Check for valid image file type (e.g., JPG, PNG, JPEG)
        $valid_types = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($_FILES['image']['type'], $valid_types)) {
            die(json_encode(["error" => "Invalid image type. Only JPG, JPEG, and PNG are allowed."]));
        }

        // Check for file size (e.g., limit to 5MB)
        if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            die(json_encode(["error" => "Image size exceeds 5MB."]));
        }

        if (!move_uploaded_file($image_tmp_name, $image_path)) {
            die(json_encode(["error" => "Image upload failed."]));
        }
    }

    // Insert data into resumedata table
    $stmt = $conn->prepare("INSERT INTO resumedata 
        (first_name, middle_name, last_name, image_path, designation, address, email, phone_no, summary,
         achievements, experiences, educations, projects, skills) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die(json_encode(["error" => "Prepare statement failed: " . $conn->error]));
    }

    $stmt->bind_param(
        "ssssssssssssss",
        $first_name,
        $middle_name,
        $last_name,
        $image_path,
        $designation,
        $address,
        $email,
        $phone_no,
        $summary,
        $achievements,
        $experiences,
        $educations,
        $projects,
        $skills
    );

    if ($stmt->execute()) {
        echo json_encode(["success" => "Resume saved successfully!"]);
    } else {
        echo json_encode(["error" => "Failed to save resume: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit; // Stop further processing
}
?>




<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Resume Page</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- custom css -->
    <link rel="stylesheet" href="main.css">
</head>

<body>
    <form action="resume.php" method="post" class="cv-form" id="cv-form" enctype="multipart/form-data">

        <nav class="navbar bg-white">
            <div class="container">
                <div class="navbar-content">
                    <div class="brand-and-toggler">
                        <a href="index1.html" class="navbar-brand">
                            <img src="template/images/curriculum-vitae.png" alt="" class="navbar-brand-icon">
                            <span class="navbar-brand-text">ResumeBuilder <span>.IT</span>
                        </a>
                        <button type="button" class="navbar-toggler-btn">
                            <div class="bars">
                                <div class="bar"></div>
                                <div class="bar"></div>
                                <div class="bar"></div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <section id="about-sc" class="">
            <div class="container">
                <div class="about-cnt">

                    <div class="cv-form-blk">
                        <div class="cv-form-row-title">
                            <h3>about section</h3>
                        </div>
                        <div class="cv-form-row cv-form-row-about">
                            <div class="cols-3">
                                <div class="form-elem">
                                    <label for="" class="form-label">First Name</label>
                                    <input name="firstname" type="text" class="form-control firstname" id=""
                                        onkeyup="generateCV()" placeholder="e.g. John" required>
                                    <span class="form-text"></span>
                                </div>
                                <div class="form-elem">
                                    <label for="" class="form-label">Middle Name <span
                                            class="opt-text">(optional)</span></label>
                                    <input name="middlename" type="text" class="form-control middlename" id=""
                                        onkeyup="generateCV()" placeholder="e.g. Herbert">
                                    <span class="form-text"></span>
                                </div>
                                <div class="form-elem">
                                    <label for="" class="form-label">Last Name</label>
                                    <input name="lastname" type="text" class="form-control lastname" id=""
                                        onkeyup="generateCV()" placeholder="e.g. Doe" required>
                                    <span class="form-text"></span>
                                </div>
                            </div>

                            <div class="cols-3">
                                <div class="form-elem">
                                    <label for="" class="form-label">Your Image</label>
                                    <input name="image" type="file" class="form-control image" id="" accept="image/*"
                                        onchange="previewImage()">
                                </div>
                                <div class="form-elem">
                                    <label for="" class="form-label">Designation</label>
                                    <input name="designation" type="text" class="form-control designation" id=""
                                        onkeyup="generateCV()" placeholder="e.g. Sr.Accountants">
                                    <span class="form-text"></span>
                                </div>
                                <div class="form-elem">
                                    <label for="" class="form-label">Address</label>
                                    <input name="address" type="text" class="form-control address" id=""
                                        onkeyup="generateCV()" placeholder="e.g. Lake Street-23">
                                    <span class="form-text"></span>
                                </div>
                            </div>

                            <div class="cols-3">
                                <div class="form-elem">
                                    <label for="" class="form-label">Email</label>
                                    <input name="email" type="text" class="form-control email" id=""
                                        onkeyup="generateCV()" placeholder="e.g. johndoe@gmail.com" required>
                                    <span class="form-text"></span>
                                </div>
                                <div class="form-elem">
                                    <label for="" class="form-label">Phone No:</label>
                                    <input name="phoneno" type="text" class="form-control phoneno" id=""
                                        onkeyup="generateCV()" placeholder="e.g. 456-768-798, 567.654.002" required>
                                    <span class="form-text"></span>
                                </div>
                                <div class="form-elem">
                                    <label for="" class="form-label">Summary</label>
                                    <input name="summary" type="text" class="form-control summary" id=""
                                        onkeyup="generateCV()" placeholder="e.g. Doe" required required>
                                    <span class="form-text"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="cv-form-blk">
                        <div class="cv-form-row-title">
                            <h3>achievements</h3>
                        </div>

                        <div class="row-separator repeater">
                            <div class="repeater" data-repeater-list="group-a">
                                <div data-repeater-item>
                                    <div class="cv-form-row cv-form-row-achievement">
                                        <div class="cols-2">
                                            <div class="form-elem">
                                                <label for="" class="form-label">Title</label>
                                                <input name="achieve_title" type="text"
                                                    class="form-control achieve_title" id="" onkeyup="generateCV()"
                                                    placeholder="e.g. johndoe@gmail.com">
                                                <span class="form-text"></span>
                                            </div>
                                            <div class="form-elem">
                                                <label for="" class="form-label">Description</label>
                                                <input name="achieve_description" type="text"
                                                    class="form-control achieve_description" id=""
                                                    onkeyup="generateCV()" placeholder="e.g. johndoe@gmail.com">
                                                <span class="form-text"></span>
                                            </div>
                                        </div>
                                        <button data-repeater-delete type="button"
                                            class="repeater-remove-btn">-</button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" data-repeater-create value="Add" class="repeater-add-btn">+</button>
                        </div>
                    </div>

                    <div class="cv-form-blk">
                        <div class="cv-form-row-title">
                            <h3>experience</h3>
                        </div>

                        <div class="row-separator repeater">
                            <div class="repeater" data-repeater-list="group-b">
                                <div data-repeater-item>
                                    <div class="cv-form-row cv-form-row-experience">
                                        <div class="cols-3">
                                            <div class="form-elem">
                                                <label for="" class="form-label">Title</label>
                                                <input name="exp_title" type="text" class="form-control exp_title" id=""
                                                    onkeyup="generateCV()">
                                                <span class="form-text"></span>
                                            </div>
                                            <div class="form-elem">
                                                <label for="" class="form-label">Company / Organization</label>
                                                <input name="exp_organization" type="text"
                                                    class="form-control exp_organization" id="" onkeyup="generateCV()">
                                                <span class="form-text"></span>
                                            </div>
                                            <div class="form-elem">
                                                <label for="" class="form-label">Location</label>
                                                <input name="exp_location" type="text" class="form-control exp_location"
                                                    id="" onkeyup="generateCV()">
                                                <span class="form-text"></span>
                                            </div>
                                        </div>

                                        <div class="cols-3">
                                            <div class="form-elem">
                                                <label for="" class="form-label">Start Date</label>
                                                <input name="exp_start_date" type="date"
                                                    class="form-control exp_start_date" id="" onkeyup="generateCV()">
                                                <span class="form-text"></span>
                                            </div>
                                            <div class="form-elem">
                                                <label for="" class="form-label">End Date</label>
                                                <input name="exp_end_date" type="date" class="form-control exp_end_date"
                                                    id="" onkeyup="generateCV()">
                                                <span class="form-text"></span>
                                            </div>
                                            <div class="form-elem">
                                                <label for="" class="form-label">Description</label>
                                                <input name="exp_description" type="text"
                                                    class="form-control exp_description" id="" onkeyup="generateCV()">
                                                <span class="form-text"></span>
                                            </div>
                                        </div>

                                        <button data-repeater-delete type="button"
                                            class="repeater-remove-btn">-</button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" data-repeater-create value="Add" class="repeater-add-btn">+</button>
                        </div>
                    </div>

                    <div class="cv-form-blk">
                        <div class="cv-form-row-title">
                            <h3>education</h3>
                        </div>

                        <div class="row-separator repeater">
                            <div class="repeater" data-repeater-list="group-c">
                                <div data-repeater-item>
                                    <div class="cv-form-row cv-form-row-experience">
                                        <div class="cols-3">
                                            <div class="form-elem">
                                                <label for="" class="form-label">School</label>
                                                <input name="edu_school" type="text" class="form-control edu_school"
                                                    id="" onkeyup="generateCV()">
                                                <span class="form-text"></span>
                                            </div>
                                            <div class="form-elem">
                                                <label for="" class="form-label">Degree</label>
                                                <input name="edu_degree" type="text" class="form-control edu_degree"
                                                    id="" onkeyup="generateCV()">
                                                <span class="form-text"></span>
                                            </div>
                                            <div class="form-elem">
                                                <label for="" class="form-label">City</label>
                                                <input name="edu_city" type="text" class="form-control edu_city" id=""
                                                    onkeyup="generateCV()">
                                                <span class="form-text"></span>
                                            </div>
                                        </div>

                                        <div class="cols-3">
                                            <div class="form-elem">
                                                <label for="" class="form-label">Start Date</label>
                                                <input name="edu_start_date" type="date"
                                                    class="form-control edu_start_date" id="" onkeyup="generateCV()">
                                                <span class="form-text"></span>
                                            </div>
                                            <div class="form-elem">
                                                <label for="" class="form-label">End Date</label>
                                                <input name="edu_graduation_date" type="date"
                                                    class="form-control edu_graduation_date" id=""
                                                    onkeyup="generateCV()">
                                                <span class="form-text"></span>
                                            </div>
                                            <div class="form-elem">
                                                <label for="" class="form-label">Description</label>
                                                <input name="edu_description" type="text"
                                                    class="form-control edu_description" id="" onkeyup="generateCV()">
                                                <span class="form-text"></span>
                                            </div>
                                        </div>

                                        <button data-repeater-delete type="button"
                                            class="repeater-remove-btn">-</button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" data-repeater-create value="Add" class="repeater-add-btn">+</button>
                        </div>
                    </div>

                    <div class="cv-form-blk">
                        <div class="cv-form-row-title">
                            <h3>projects</h3>
                        </div>

                        <div class="row-separator repeater">
                            <div class="repeater" data-repeater-list="group-d">
                                <div data-repeater-item>
                                    <div class="cv-form-row cv-form-row-experience">
                                        <div class="cols-3">
                                            <div class="form-elem">
                                                <label for="" class="form-label">Project Name</label>
                                                <input name="proj_title" type="text" class="form-control proj_title"
                                                    id="" onkeyup="generateCV()">
                                                <span class="form-text"></span>
                                            </div>
                                            <div class="form-elem">
                                                <label for="" class="form-label">Project link</label>
                                                <input name="proj_link" type="text" class="form-control proj_link" id=""
                                                    onkeyup="generateCV()">
                                                <span class="form-text"></span>
                                            </div>
                                            <div class="form-elem">
                                                <label for="" class="form-label">Description</label>
                                                <input name="proj_description" type="text"
                                                    class="form-control proj_description" id="" onkeyup="generateCV()">
                                                <span class="form-text"></span>
                                            </div>
                                        </div>
                                        <button data-repeater-delete type="button"
                                            class="repeater-remove-btn">-</button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" data-repeater-create value="Add" class="repeater-add-btn">+</button>
                        </div>
                    </div>

                    <div class="cv-form-blk">
                        <div class="cv-form-row-title">
                            <h3>skills</h3>
                        </div>

                        <div class="row-separator repeater">
                            <div class="repeater" data-repeater-list="group-e">
                                <div data-repeater-item>
                                    <div class="cv-form-row cv-form-row-skills">
                                        <div class="form-elem">
                                            <label for="" class="form-label">Skill</label>
                                            <input name="skill" type="text" class="form-control skill" id=""
                                                onkeyup="generateCV()">
                                            <span class="form-text"></span>
                                        </div>

                                        <button data-repeater-delete type="button"
                                            class="repeater-remove-btn">-</button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" data-repeater-create value="Add" class="repeater-add-btn">+</button>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section id="preview-sc" class="print_area">
            <div class="container">
                <div class="preview-cnt">
                    <div class="preview-cnt-l bg-green text-white">
                        <div class="preview-blk">
                            <div class="preview-image">
                                <img src="" alt="" id="image_dsp">
                            </div>
                            <div class="preview-item preview-item-name">
                                <span class="preview-item-val fw-6" id="fullname_dsp"></span>
                            </div>
                            <div class="preview-item">
                                <span class="preview-item-val text-uppercase fw-6 ls-1" id="designation_dsp"></span>
                            </div>
                        </div>

                        <div class="preview-blk">
                            <div class="preview-blk-title">
                                <h3>about</h3>
                            </div>
                            <div class="preview-blk-list">
                                <div class="preview-item">
                                    <span class="preview-item-val" id="phoneno_dsp"></span>
                                </div>
                                <div class="preview-item">
                                    <span class="preview-item-val" id="email_dsp"></span>
                                </div>
                                <div class="preview-item">
                                    <span class="preview-item-val" id="address_dsp"></span>
                                </div>
                                <div class="preview-item">
                                    <span class="preview-item-val" id="summary_dsp"></span>
                                </div>
                            </div>
                        </div>

                        <div class="preview-blk">
                            <div class="preview-blk-title">
                                <h3>skills</h3>
                            </div>
                            <div class="skills-items preview-blk-list" id="skills_dsp">
                                <!-- skills list here -->
                            </div>
                        </div>
                    </div>

                    <div class="preview-cnt-r bg-white">
                        <div class="preview-blk">
                            <div class="preview-blk-title">
                                <h3>Achievements</h3>
                            </div>
                            <div class="achievements-items preview-blk-list" id="achievements_dsp"></div>
                        </div>

                        <div class="preview-blk">
                            <div class="preview-blk-title">
                                <h3>educations</h3>
                            </div>
                            <div class="educations-items preview-blk-list" id="educations_dsp"></div>
                        </div>

                        <div class="preview-blk">
                            <div class="preview-blk-title">
                                <h3>experiences</h3>
                            </div>
                            <div class="experiences-items preview-blk-list" id="experiences_dsp"></div>
                        </div>

                        <div class="preview-blk">
                            <div class="preview-blk-title">
                                <h3>projects</h3>
                            </div>
                            <div class="projects-items preview-blk-list" id="projects_dsp"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="print-btn-sc">
            <div class="btn-container">
                <div class="container">
                    <button type="submit" id="submitButton" class="btn btn-primary">Submit</button>
                    <button type="button" class="print-btn btn btn-primary" onclick="printCV()">Print CV</button>
                </div>

            </div>

        </section>




        <!-- jquery cdn -->
        <script src="https://code.jquery.com/jquery-3.6.4.js"
            integrity="sha256-a9jBBRygX1Bh5lt8GZjXDzyOB+bWve9EiO7tROUtj/E=" crossorigin="anonymous"></script>
        <!-- jquery repeater cdn -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.js"
            integrity="sha512-bZAXvpVfp1+9AUHQzekEZaXclsgSlAeEnMJ6LfFAvjqYUVZfcuVXeQoN5LhD7Uw0Jy4NCY9q3kbdEXbwhZUmUQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <!-- custom js -->
        <script src="script.js"></script>
        <!-- app js -->
        <script src="app.js"></script>
    </form>
</body>

</html>