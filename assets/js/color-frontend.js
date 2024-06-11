popupButton.addEventListener("click", function() {
    var selectedAttribute = getSelectedAttributes();

    // Check if the attribute 'attribute_pa_inhoud' is selected
    if (selectedAttribute.attribute_pa_inhoud !== '') {
        myPopup.classList.add("show"); // Show the popup
    } else {
        alert("Please select the attribute before continuing."); // Show an alert
    }
});
window.addEventListener(
    "click",
    function(event) {
        if (event.target == myPopup) {
            myPopup.classList.remove(
                "show"
            );
        }
    }
);

window.addEventListener(
    "click",
    function(event) {
        if (event.target == myPopup) {
            myPopup.classList.remove(
                "show"
            );
        }
    }
);
document.addEventListener("DOMContentLoaded", function() {
    createBoxes();
    removeEmptyFilters();
});

function createBoxes() {
    var popUpcontent = document.getElementsByClassName("popup-content")[0]; // Corrected getElementByClassName to getElementsByClassName
    var colorContent = document.getElementById("color-content");
    var colorHeader = document.getElementById("popup-header");
    var colorResult = document.getElementById("color-result");
    var customColorField = document.getElementById("custom_color_field");
    var popupButton = document.getElementById("popupButton");
    var filterInput = document.getElementById("filterInput"); // Get the filter input element

    // Select all elements with the class 'color-filter-choice'
    var elements = document.querySelectorAll('.color-filter-choice');

    // Add event listener for 'input' event
    filterInput.addEventListener('input', function() {
        var filterValue = filterInput.value.toLowerCase();
        console.log(filterValue); // Optionally log the filter value to the console
        updateColorChoices(filterValue); // Call function to update color choices based on filter
    });

    // Loop through the NodeList and attach an event listener to each element
    elements.forEach(function(element) {
        element.addEventListener('click', function() {
            var filterValue = this.getAttribute("data-colorfilter").toLowerCase(); // 'this' refers to the current element in the loop
            console.log(filterValue);
            updateColorChoices(filterValue);
        });
    });

    var popupCloseButton = document.getElementById('popupClose');
    // Event listener to close the popup when clicking on the close button
    popupCloseButton.addEventListener("click", function() {
        myPopup.classList.remove("show");
    });

    function updateColorChoices(filter) {
        colorContent.innerHTML = ''; // Clear existing color choices before appending filtered ones

        // Sort colorData by the 'order' attribute
        let sortedColors = colorData.sort((a, b) => a.order - b.order);

        sortedColors.forEach(function(color) {
            if (color.filter.toLowerCase().includes(filter) || color.name.toLowerCase().includes(filter)) { // Check if the color name includes the filter text
                var colorChoice = document.createElement("div");
                colorChoice.classList.add("color-choice");
                colorChoice.style.backgroundColor = color.color;
                colorChoice.classList.add(color.theme);
                colorChoice.setAttribute("data-id", color.id);
                colorChoice.setAttribute("data-name", color.name);
                colorChoice.setAttribute("data-color", color.color);
                colorChoice.setAttribute("data-group", color.pricegroupId);
                colorChoice.setAttribute("data-theme", color.theme);
                colorChoice.setAttribute("data-order", color.order);
                colorChoice.setAttribute("data-filter", color.filter);
                colorChoice.innerHTML = '<span>' + color.name + '<br>P' + color.pricegroupId + '</span>';

                colorChoice.addEventListener("click", function(event) {
                    event.preventDefault(); // Prevent any default action triggered by clicking the color choice.
                    colorContent.style.display = 'none';
                    colorHeader.style.display = 'none';
                    colorResult.style.display = 'block';
                    colorResult.innerHTML = '';

                    var controlPanel = document.createElement("div");
                    controlPanel.style.display = 'flex';
                    controlPanel.style.justifyContent = 'space-between';
                    controlPanel.style.alignItems = 'center';
                    controlPanel.style.width = '100%';
                    controlPanel.classList.add("color-controlpanel");

                    var nameDiv = document.createElement("div");
                    nameDiv.innerHTML = this.getAttribute("data-name") + '<br><span style="font-weight:400">' + this.getAttribute("data-group") + '</span>';
                    nameDiv.classList.add("color-selected-name");
                    nameDiv.style.flex = '1'; // Name takes half the width
                    jQuery(customColorField).val(this.getAttribute("data-id"));
                    popupButton.style.backgroundColor = this.style.backgroundColor;
                    popupButton.textContent = this.getAttribute("data-name");
                    var price = calculateNow(); // Assuming this function recalculates something important

                    // New price div
                    var priceDiv = document.createElement("div");
                    priceDiv.textContent = price; // Assuming price attribute contains the price
                    priceDiv.classList.add("color-selected-price");
                    priceDiv.style.flex = '1'; // Price takes one third of the remaining width

                    var buttonContainer = document.createElement("div");
                    buttonContainer.style.display = 'flex';
                    buttonContainer.style.flex = '2'; // Container for buttons takes the other half

                    var button1 = document.createElement("button");
                    button1.classList.add("button-color");
                    button1.classList.add("button-color-select");
                    button1.textContent = "Selecteer deze kleur";
                    button1.style.flex = '1'; // Each button takes half of the button container space

                    var button2 = document.createElement("button");
                    button2.classList.add("button-color");
                    button2.classList.add("button-color-back");
                    button2.textContent = "Terug naar het overzicht";
                    button2.style.flex = '1';

                    // Append buttons to their container
                    buttonContainer.appendChild(button1);
                    buttonContainer.appendChild(button2);

                    // Append name, price and button container to the control panel
                    controlPanel.appendChild(nameDiv);
                    controlPanel.appendChild(priceDiv);
                    controlPanel.appendChild(buttonContainer);

                    var colorDisplay = document.createElement("div");
                    colorDisplay.style.backgroundColor = this.style.backgroundColor;
                    colorDisplay.style.width = "100%";
                    colorDisplay.style.height = "300px";
                    colorDisplay.style.borderRadius = "20px";

                    // Append all elements to the popup content
                    colorResult.appendChild(controlPanel);
                    colorResult.appendChild(colorDisplay);

                    // Event handler for the 'Selecteer deze kleur' button
                    button1.addEventListener("click", function(event) {
                        event.preventDefault(); // Prevent the button from doing default actions like submitting form.
                        myPopup.classList.remove("show");
                    });

                    // Event handler for the 'Terug naar het overzicht' button
                    button2.addEventListener("click", function(event) {
                        event.preventDefault(); // Prevent the button from doing default actions like submitting form.
                        colorContent.style.display = 'grid';
                        colorHeader.style.display = 'block';
                        colorResult.style.display = 'none';
                        createBoxes(); // Reinitialize the boxes if needed
                    });
                });

                colorContent.appendChild(colorChoice);
            }
        });
    }

    updateColorChoices(''); // Initially call with no filter to display all colors
}

function removeEmptyFilters() {
    var filterBar = document.querySelector('.color-filter-bar');
    var filters = document.querySelectorAll('.color-filter-choice');
    // Sort colorData by the 'order' attribute
    let sortedColors = colorData.sort((a, b) => a.order - b.order);


    filters.forEach(function(filter) {
        var filterColor = filter;


        sortedColors.forEach(function(color) {
            if (color.filter === filterColor) {
                console.log(color.filter);
                console.log(filterColor);
            }

        });
    });

    if (document.querySelectorAll('.color-filter-choice').length === 0) {
        filterBar.style.display = 'none';
    }
}


function calculateCustomPrice(clickedColorId) {
    if (!clickedColorId) {
        return false;
    }

    selectedRow = findColorById(clickedColorId);

    selectedContents = getSelectedAttributes();

    if (selectedRow) {
        if (productData.productType == 'variable') {
            var originalPrice = findPriceBySlug(selectedRow.pricegroup, selectedContents.attribute_pa_inhoud);
            var varId = jQuery('input.variation_id').val(); // Get the current variation ID
            if (varId) {
                //alert('You just selected variation #' + varId);
                var additionalCosts = displayVariationPrice(varId);
            }
        } else {
            var additionalCosts = productData.regularPrice;
            var originalPrice = findPriceBySlug(selectedRow.pricegroup, 'single');
        }




        if (originalPrice && additionalCosts) {
            var numericOriginalPrice = +originalPrice;
            var numericAdditionalCosts = +additionalCosts;
            return numericOriginalPrice + numericAdditionalCosts;
        }

        return originalPrice;
    }
    return false;



}
// Function to find price by slug
function findPriceBySlug(pricegroup, slug) {
    var result = pricegroup.find(item => item.slug === slug);

    return result ? result.price : undefined; // Return the price if found, otherwise undefined
}



function getSelectedAttributes() {
    var selectedAttributes = {};

    // Get the WooCommerce variation form
    var variationForm = document.querySelector("form.variations_form");

    // Check if the variation form exists
    if (variationForm) {
        // Loop through each select element in the variation form
        variationForm.querySelectorAll("select").forEach(function(select) {
            var attribute = select.getAttribute("name");
            var value = select.value;

            // Add the selected attribute and value to the object
            selectedAttributes[attribute] = value;
        });
    }

    // Return the selected attributes object
    return selectedAttributes;
}

// Function to find color by ID
function findColorById(id) {
    for (var i = 0; i < colorData.length; i++) {
        if (colorData[i].id == id) {
            return colorData[i];
        }
    }
    // If color with the given ID is not found, return null or handle as needed
    return null;
}

// Example usage:
document.addEventListener("DOMContentLoaded", function() {
    // Call the function to get the selected attributes
    var selectedAttributes = getSelectedAttributes();
});



// Function to display the price of the selected variation
function displayVariationPrice(variationId) {
    var form = jQuery('.variations_form');
    var variations = form.data('product_variations'); // Get the variations data from the data attribute
    var foundVariation = variations.find(variation => variation.variation_id == variationId);

    if (foundVariation) {
        var priceHtml = foundVariation.price_html; // Get the price HTML
        jQuery('.woocommerce-variation-price').html(priceHtml); // Display the price HTML in the appropriate container
        return foundVariation.display_price;
    } else {
        console.log('Variation with ID ' + variationId + ' not found.');
    }
}

function formatPrice(price) {
    // Convert the price to a string with 2 decimal places
    price = Number(price);
    var formattedPrice = price.toFixed(2);

    // Replace dots with commas for the thousands separator
    formattedPrice = formattedPrice.replace(/\./g, ',');

    // Split the price into integer and decimal parts
    var parts = formattedPrice.split(',');

    // Add dots for the thousands separator
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    // Join the parts back together with a comma
    formattedPrice = parts.join(',');

    // Add the Euro sign and return the formatted price
    return '€ ' + formattedPrice;
}

function calculateNow() {
    var clickedColorId = jQuery('#custom_color_field').val(); // Get the value of the input field

    var newPrice = calculateCustomPrice(clickedColorId);

    if (newPrice) {

        var htmlPrice = formatPrice(newPrice);
        var customColorPriceElement = document.querySelector('.custom-color-price');
        if (customColorPriceElement) {
            customColorPriceElement.textContent = htmlPrice;
        }

    } else {
        var customColorPriceElement = document.querySelector('.custom-color-price');
        if (customColorPriceElement) {
            customColorPriceElement.textContent = htmlPrice;
        }

    }
    return htmlPrice;
}


jQuery(document).ready(function($) {
    // Listen for changes in the variation ID input field
    $('input.variation_id').change(function() {
        calculateNow();
    });
});