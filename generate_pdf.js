document.getElementById('myForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission
  
    const formData = new FormData(document.getElementById('myForm'));
  
    fetch('generate_pdf.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.blob())
    .then(blob => {
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = 'form_data.pdf';
      link.click();
    });
  });
  