// Regex for validation
const strRegex = /^[a-zA-Z\s]*$/; // Only letters and spaces
const emailRegex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
const phoneRegex = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;

// Form and input elements
const mainForm = document.getElementById("cv-form");
const firstnameElem = mainForm.firstname;
const middlenameElem = mainForm.middlename;
const lastnameElem = mainForm.lastname;
const imageElem = mainForm.image;
const designationElem = mainForm.designation;
const addressElem = mainForm.address;
const emailElem = mainForm.email;
const phonenoElem = mainForm.phoneno;
const summaryElem = mainForm.summary;

// Display elements for preview (IDs must match your HTML)
const nameDsp = document.getElementById("fullname_dsp");
const designationDsp = document.getElementById("designation_dsp");
const phonenoDsp = document.getElementById("phoneno_dsp");
const emailDsp = document.getElementById("email_dsp");
const addressDsp = document.getElementById("address_dsp");
const summaryDsp = document.getElementById("summary_dsp");
const skillsDsp = document.getElementById("skills_dsp");
const achievementsDsp = document.getElementById("achievements_dsp");
const educationsDsp = document.getElementById("educations_dsp");
const experiencesDsp = document.getElementById("experiences_dsp");
const projectsDsp = document.getElementById("projects_dsp");
const imageDsp = document.getElementById("image_dsp");

// Function to validate required fields
const validateRequiredFields = () => {
    let errors = [];
    if (!firstnameElem.value.trim()) {
        errors.push("First Name is required.");
    }
    if (!lastnameElem.value.trim()) {
        errors.push("Last Name is required.");
    }
    if (!emailElem.value.trim()) {
        errors.push("Email is required.");
    }
    if (!emailRegex.test(emailElem.value.trim())) {
        errors.push("Please enter a valid email address.");
    }
    return errors;
};

// Function to fetch values from repeater fields
const fetchValues = (attrs, ...nodeLists) => {
    let tempDataArr = [];
    for (let i = 0; i < nodeLists[0].length; i++) {
        let dataObj = {};
        for (let j = 0; j < attrs.length; j++) {
            dataObj[attrs[j]] = nodeLists[j][i].value.trim();
        }
        tempDataArr.push(dataObj);
    }
    return tempDataArr;
};

// Get user inputs from the form
const getUserInputs = () => {
    return {
        firstname: firstnameElem.value.trim(),
        middlename: middlenameElem.value.trim(),
        lastname: lastnameElem.value.trim(),
        designation: designationElem.value.trim(),
        address: addressElem.value.trim(),
        email: emailElem.value.trim(),
        phoneno: phonenoElem.value.trim(),
        summary: summaryElem.value.trim(),
        achievements: fetchValues(
            ["achieve_title", "achieve_description"],
            document.querySelectorAll(".achieve_title"),
            document.querySelectorAll(".achieve_description")
        ),
        experiences: fetchValues(
            [
                "exp_title",
                "exp_organization",
                "exp_location",
                "exp_start_date",
                "exp_end_date",
                "exp_description",
            ],
            document.querySelectorAll(".exp_title"),
            document.querySelectorAll(".exp_organization"),
            document.querySelectorAll(".exp_location"),
            document.querySelectorAll(".exp_start_date"),
            document.querySelectorAll(".exp_end_date"),
            document.querySelectorAll(".exp_description")
        ),
        educations: fetchValues(
            [
                "edu_school",
                "edu_degree",
                "edu_city",
                "edu_start_date",
                "edu_graduation_date",
                "edu_description",
            ],
            document.querySelectorAll(".edu_school"),
            document.querySelectorAll(".edu_degree"),
            document.querySelectorAll(".edu_city"),
            document.querySelectorAll(".edu_start_date"),
            document.querySelectorAll(".edu_graduation_date"),
            document.querySelectorAll(".edu_description")
        ),
        projects: fetchValues(
            ["proj_title", "proj_link", "proj_description"],
            document.querySelectorAll(".proj_title"),
            document.querySelectorAll(".proj_link"),
            document.querySelectorAll(".proj_description")
        ),
        skills: fetchValues(["skill"], document.querySelectorAll(".skill")),
    };
};

// Function to display the CV preview
const displayCV = (userData) => {
    nameDsp.innerHTML = `${userData.firstname} ${userData.middlename} ${userData.lastname}`;
    phonenoDsp.innerHTML = userData.phoneno;
    emailDsp.innerHTML = userData.email;
    addressDsp.innerHTML = userData.address;
    designationDsp.innerHTML = userData.designation;
    summaryDsp.innerHTML = userData.summary;

    const showListData = (listData, listContainer) => {
        listContainer.innerHTML = "";
        listData.forEach((item) => {
            const itemElem = document.createElement("div");
            itemElem.classList.add("preview-item");
            for (const key in item) {
                const subItemElem = document.createElement("span");
                subItemElem.classList.add("preview-item-val");
                subItemElem.innerHTML = item[key];
                itemElem.appendChild(subItemElem);
            }
            listContainer.appendChild(itemElem);
        });
    };

    showListData(userData.achievements, achievementsDsp);
    showListData(userData.experiences, experiencesDsp);
    showListData(userData.educations, educationsDsp);
    showListData(userData.projects, projectsDsp);
    showListData(userData.skills, skillsDsp);
};

// Handle form submission
const handleFormSubmit = (event) => {
    event.preventDefault();

    const inputs = getUserInputs();
    // Validate required fields
    if (
        !inputs.firstname ||
        !inputs.lastname ||
        !inputs.email ||
        !emailRegex.test(inputs.email)
    ) {
        alert("First Name, Last Name, and a valid Email are required!");
        return;
    }

    // Disable the submit button to prevent multiple submissions
    const submitButton = document.getElementById("submitButton");
    submitButton.disabled = true;
    submitButton.textContent = "Submitting...";

    // Create FormData object
    const formData = new FormData(mainForm);
    // Append JSON data for repeater fields
    formData.append("achievements", JSON.stringify(inputs.achievements));
    formData.append("experiences", JSON.stringify(inputs.experiences));
    formData.append("educations", JSON.stringify(inputs.educations));
    formData.append("projects", JSON.stringify(inputs.projects));
    formData.append("skills", JSON.stringify(inputs.skills));

    // Send data to server via fetch
    fetch("resume.php", {
        method: "POST",
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
            submitButton.disabled = false;
            submitButton.textContent = "Submit";
            if (data.error) {
                alert("Error: " + data.error);
            } else if (data.success) {
                alert(data.success);
            }
        })
        .catch((error) => {
            submitButton.disabled = false;
            submitButton.textContent = "Submit";
            console.error("Error:", error);
            alert("Error saving resume data.");
        });
};

// Preview uploaded image
const previewImage = () => {
    const oFReader = new FileReader();
    oFReader.readAsDataURL(imageElem.files[0]);
    oFReader.onload = (ofEvent) => {
        imageDsp.src = ofEvent.target.result;
    };
};

// Print CV
const printCV = () => {
    window.print();
};

const generateCV = () => {
    const userData = getUserInputs();
    displayCV(userData);
}

// Event listeners
document.addEventListener("DOMContentLoaded", function () {
    mainForm.addEventListener("input", generateCV);
    imageElem.addEventListener("change", previewImage);
    document.querySelector(".print-btn").addEventListener("click", printCV);
    document.getElementById("submitButton").addEventListener("click", handleFormSubmit);

    // Generate initial CV preview
    generateCV();
});
