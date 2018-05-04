<!-- @TODO Move texts into lang file [Kovah 2018-05-04] -->
<h2>Welcome to InvoicePlane</h2>

<p class="mt-5 mb-5">Thank you for choosing InvoicePlane to manage your clients, quotes, invoices and payments. This
    setup will guide you trough the initial configuration of both your database and the application. First, please
    select a language to confinue.</p>

<form action="/setup/" method="post">

    <?php _csrf_field(); ?>

    <div class="form-group">
        <label for="language-select">Choose a language</label>
        <select class="form-control" id="language-select" name="language-select">
            <!-- @TODO Replace with language list from controller [Kovah 2018-05-04] -->
            <option>English</option>
            <option>Deutsch</option>
            <option>Español</option>
        </select>
    </div>

    <button class="btn btn-primary" name="btn_continue" value="1">Continue</button>

</form>
