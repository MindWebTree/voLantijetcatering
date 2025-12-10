<script>
    function printPDF() {
        // Open the PDF in a new window or tab
        var pdfUrl = '/path/to/your/pdf'; // Replace with the actual URL of your PDF
        var win = window.open(pdfUrl, '_blank');

        // Once the window is open, trigger the print dialog
        win.onload = function() {
            win.print();
        };
    }
</script>
