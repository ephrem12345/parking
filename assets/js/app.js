document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.alert');
    if (alerts.length) {
        setTimeout(() => alerts.forEach(alert => alert.classList.add('fade')), 8000);
    }
});

function showSuccess(message) {
    Swal.fire({icon:'success', title:'Success', text:message, timer:2500, toast:true, position:'top-end', showConfirmButton:false});
}

function showError(message) {
    Swal.fire({icon:'error', title:'Error', text:message, timer:3000, toast:true, position:'top-end', showConfirmButton:false});
}
