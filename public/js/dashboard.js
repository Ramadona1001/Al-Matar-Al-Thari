// Dashboard Custom JavaScript

$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Confirmation for delete buttons
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        
        if (confirm('Are you sure you want to delete this item?')) {
            form.submit();
        }
    });

    // Loading state for form submissions
    $('form').on('submit', function() {
        var submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...');
    });

    // AJAX setup for CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Real-time search functionality
    $('#searchInput').on('keyup', function() {
        var searchValue = $(this).val().toLowerCase();
        var searchTable = $(this).data('table');
        
        if (searchTable) {
            $('#' + searchTable + ' tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(searchValue) > -1);
            });
        }
    });

    // Date range picker initialization
    if ($('.date-range-picker').length) {
        $('.date-range-picker').daterangepicker({
            opens: 'left',
            locale: {
                format: 'YYYY-MM-DD',
                applyLabel: 'Apply',
                cancelLabel: 'Cancel',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom',
                weekLabel: 'W',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
            }
        });
    }

    // Chart initialization helper
    window.initChart = function(ctx, type, data, options = {}) {
        return new Chart(ctx, {
            type: type,
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                ...options
            }
        });
    };

    // Print functionality
    $('.print-btn').on('click', function() {
        window.print();
    });

    // Export functionality
    $('.export-btn').on('click', function() {
        var format = $(this).data('format');
        var table = $(this).data('table');
        
        if (format === 'csv') {
            exportTableToCSV(table);
        } else if (format === 'pdf') {
            exportTableToPDF(table);
        }
    });

    // CSV Export function
    function exportTableToCSV(tableId) {
        var table = $('#' + tableId);
        var csv = [];
        
        table.find('thead tr').each(function() {
            var row = [];
            $(this).find('th').each(function() {
                row.push($(this).text().trim());
            });
            csv.push(row.join(','));
        });
        
        table.find('tbody tr:visible').each(function() {
            var row = [];
            $(this).find('td').each(function() {
                row.push($(this).text().trim());
            });
            csv.push(row.join(','));
        });
        
        var csvContent = csv.join('\n');
        var blob = new Blob([csvContent], { type: 'text/csv' });
        var url = window.URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'export_' + new Date().getTime() + '.csv';
        a.click();
    }

    // PDF Export function (simplified version)
    function exportTableToPDF(tableId) {
        alert('PDF export functionality would be implemented here using a library like jsPDF or similar.');
    }

    // Notification handler
    window.showNotification = function(message, type = 'info') {
        var alertClass = 'alert-' + type;
        var notification = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert" style="z-index: 9999;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        $('body').append(notification);
        
        setTimeout(function() {
            $('.alert').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    };

    // AJAX error handler
    $(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
        if (jqXHR.status === 401) {
            showNotification('Your session has expired. Please login again.', 'warning');
            setTimeout(function() {
                window.location.href = '/login';
            }, 2000);
        } else if (jqXHR.status === 403) {
            showNotification('You do not have permission to perform this action.', 'danger');
        } else if (jqXHR.status === 500) {
            showNotification('An internal server error occurred. Please try again later.', 'danger');
        }
    });

    // Image preview functionality
    $('.image-upload-input').on('change', function() {
        var input = this;
        var preview = $(this).data('preview');
        
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                $('#' + preview).attr('src', e.target.result);
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    });

    // Auto-resize textareas
    $('textarea.auto-resize').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Smooth scrolling for anchor links
    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 100
            }, 1000);
        }
    });

    // Back to top button
    var backToTop = $('.back-to-top');
    
    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            backToTop.fadeIn();
        } else {
            backToTop.fadeOut();
        }
    });
    
    backToTop.on('click', function() {
        $('html, body').animate({
            scrollTop: 0
        }, 800);
        return false;
    });

    // Dark mode toggle (if implemented)
    $('.dark-mode-toggle').on('click', function() {
        $('body').toggleClass('dark-mode');
        localStorage.setItem('darkMode', $('body').hasClass('dark-mode'));
    });

    // Check for saved dark mode preference
    if (localStorage.getItem('darkMode') === 'true') {
        $('body').addClass('dark-mode');
    }

    // Initialize any tooltips in the content
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Custom file input styling
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });

    // Prevent double submission of forms
    $('form').on('submit', function() {
        var $this = $(this);
        if ($this.data('submitted')) {
            return false;
        }
        $this.data('submitted', true);
    });

    // Reset form submission flag when page is loaded
    $(window).on('pageshow', function() {
        $('form').data('submitted', false);
    });

    // Initialize any select2 elements
    if ($('.select2').length) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }

    // Initialize date pickers
    if ($('.datepicker').length) {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    }

    // Initialize time pickers
    if ($('.timepicker').length) {
        $('.timepicker').timepicker({
            showInputs: false,
            showMeridian: false,
            defaultTime: 'current'
        });
    }

    console.log('Dashboard JavaScript initialized successfully');
});