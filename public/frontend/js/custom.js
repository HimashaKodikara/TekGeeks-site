$(document).ready(function () {

  // ✅ Fix Bootstrap modal focus issue with Select2
  if ($.fn.modal) {
    $.fn.modal.Constructor.prototype.enforceFocus = function () {
      var $element = this.$element;
      $(document)
        .off('focusin.bs.modal')
        .on('focusin.bs.modal', $.proxy(function (e) {
          if (
            $element[0] !== e.target &&
            !$element.has(e.target).length &&
            !$(e.target).closest('.select2-container').length
          ) {
            $element.trigger('focus');
          }
        }, this));
    };
  }



  // ✅ Initialize all Select2 dropdowns safely
  $('.select2_dropdown').each(function () {
    var $select = $(this);

    // Avoid double initialization
    if ($select.hasClass('select2-hidden-accessible')) return;

    // Base options
    var options = {
      width: '100%',
      allowClear: true
    };

    // --- Case 1: Tag input ---
    if ($select.hasClass('allow-tags')) {
      options.tags = true;
      options.placeholder = "Type to Search or Manually Enter";
    }

    // --- Case 2: Normal dropdown (no search) ---
    else if ($select.hasClass('normal-dropdown')) {
      options.minimumResultsForSearch = Infinity; // disables search
      options.placeholder = "Select an Option from the List";
    }

    // --- Case 3: Default dropdown (none of the above) ---
    else {
      options.placeholder = "Search and Select from the List";
    }

    // Dropdown parent (fix overflow issues)
    var $rootModal = $select.closest('.modal');
    if ($rootModal.length) {
      options.dropdownParent = $rootModal;
    } else {
      var $wrapper = $select.closest('.select2-wrapper');
      if ($wrapper.length) options.dropdownParent = $wrapper;
    }

    // Initialize Select2
    $select.select2(options);
  });

  // Optional: change log
  $('.select2_dropdown').on('change', function () {
    console.log('Selected value:', $(this).val());
  });

});




// Select 2 js

// ====================================================================
// ====================================================================
// ====================================================================
// ====================================================================
// ====================================================================
// ====================================================================

// show hide btn row

document.addEventListener('DOMContentLoaded', function() {
  const radioHide = document.querySelector('.hide_btn_row');
  const radioShow = document.querySelector('.show_btn_row');
  const assetsSection = document.querySelector('.main_btn_row');
  
  // Hide section when hide_btn_row is clicked
  radioHide.addEventListener('change', function() {
    if (this.checked) {
      assetsSection.style.setProperty('display', 'none', 'important');
    }
  });
  
  // Show section when show_btn_row is clicked
  radioShow.addEventListener('change', function() {
    if (this.checked) {
      assetsSection.style.setProperty('display', 'flex', 'important');
    }
  });
});

// ===================================================================
// ===================================================================
// ===================================================================
// ===================================================================
// ===================================================================

// tooltip

const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

