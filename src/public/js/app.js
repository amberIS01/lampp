// Mini ERP - Client-side JS

// Delete confirmation
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });
});
