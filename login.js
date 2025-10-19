document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    const errorMessage = document.getElementById('error-message');

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault(); // ป้องกันไม่ให้ฟอร์มรีเฟรชหน้า
        errorMessage.style.display = 'none';

        const formData = new FormData(loginForm);
        
        try {
            const response = await fetch('auth.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // ถ้าล็อกอินสำเร็จ ให้ไปที่หน้า display
                window.location.href = 'display.php';
            } else {
                // ถ้าไม่สำเร็จ ให้แสดงข้อความผิดพลาด
                errorMessage.textContent = result.message;
                errorMessage.style.display = 'block';
            }
        } catch (error) {
            console.error('Error:', error);
            errorMessage.textContent = 'เกิดข้อผิดพลาดในการเชื่อมต่อ';
            errorMessage.style.display = 'block';
        }
    });
});