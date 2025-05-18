export function togglePasswordVisibility(fieldId = 'password') {
    const passwordInput = document.getElementById(fieldId);
    const showIcon = document.getElementById(`show-${fieldId}-icon`);
    const hideIcon = document.getElementById(`hide-${fieldId}-icon`);

    if (!passwordInput) return;

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        if (showIcon) showIcon.style.display = 'none';
        if (hideIcon) hideIcon.style.display = 'block';
    } else {
        passwordInput.type = 'password';
        if (showIcon) showIcon.style.display = 'block';
        if (hideIcon) hideIcon.style.display = 'none';
    }
}

window.togglePasswordVisibility = togglePasswordVisibility;
