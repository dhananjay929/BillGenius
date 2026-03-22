document.addEventListener("DOMContentLoaded", function () {
    const dropdown = document.getElementById("discom");
    const extraFieldsContainer = document.getElementById("extraFields");
    const form = document.getElementById("discom_form");
    const notification = document.getElementById("notification");
    const closeNotification = document.getElementById("closeNotification");
    const loader = document.getElementById("loader");
    const fetchBtn = document.getElementById("fetchBtn");
    const resetBtn = document.getElementById("resetBtn");

    // Populate the dropdown dynamically from fieldMappings
    for (const key in fieldMappings) {
        let option = document.createElement("option");
        option.value = key;
        option.textContent = key;
        dropdown.appendChild(option);
    }

    // Restore form state from localStorage
    function restoreFormState() {
        const savedData = JSON.parse(localStorage.getItem("formData"));
        if (savedData) {
            if (savedData.discom_name) {
                dropdown.value = savedData.discom_name;
                dropdown.dispatchEvent(new Event("change"));
            }

            setTimeout(() => {
                for (const key in savedData) {
                    let input = document.querySelector(`input[name="${key}"]`);
                    if (input) input.value = savedData[key];
                }
            }, 200);
        }
    }

    // Event Listener for Dropdown Selection
    dropdown.addEventListener("change", function () {
        extraFieldsContainer.innerHTML = "";
        let selectedOption = this.value;

        if (selectedOption && fieldMappings[selectedOption]) {
            fieldMappings[selectedOption].forEach(field => {
                let inputElement = document.createElement("input");
                inputElement.type = field.type;
                inputElement.name = field.name;
                inputElement.placeholder = field.placeholder;
                inputElement.className = "w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400";
                inputElement.required = true;

                let label = document.createElement("label");
                label.className = "block font-medium text-gray-600";
                label.textContent = field.label + ":";

                let div = document.createElement("div");
                div.appendChild(label);
                div.appendChild(inputElement);

                extraFieldsContainer.appendChild(div);
            });
            extraFieldsContainer.classList.remove("hidden");
            restoreFormState();
        } else {
            extraFieldsContainer.classList.add("hidden");
        }
    });

    // Save Form Data to localStorage Before Submitting
    form.addEventListener("submit", function () {
        let formData = {};
        formData["discom_name"] = dropdown.value;

        const inputs = extraFieldsContainer.querySelectorAll("input");
        inputs.forEach(input => {
            formData[input.name] = input.value;
        });

        localStorage.setItem("formData", JSON.stringify(formData));

        // Show Loader
        loader.classList.remove("hidden");
        fetchBtn.disabled = true;
    });

    // Reset Form on Click
    resetBtn.addEventListener("click", function () {
        form.reset(); // Reset form fields
        extraFieldsContainer.innerHTML = ""; // Remove dynamic fields
        localStorage.removeItem("formData"); // Clear saved data
        dropdown.value = ""; // Reset dropdown
        extraFieldsContainer.classList.add("hidden");
        notification.classList.add("hidden");
    });

    // Restore Form on Page Load
    restoreFormState();

    // Hide Loader When Page Reloads with Data
    window.addEventListener("load", function () {
        loader.classList.add("hidden");
        fetchBtn.disabled = false;
    });

    // Close Notification on Click
    closeNotification.addEventListener("click", function () {
        notification.classList.add("hidden");
    });
});
