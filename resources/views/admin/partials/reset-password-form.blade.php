<style>
    :root {
        --primary: #E2E689;
        --primary-dark: #bfcf5b;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f4f4;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    form#resetPasswordForm {
        background-color: #fff;
        margin-left: -33px;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 400px;
    }

    form#resetPasswordForm h2 {
        text-align: center;
        margin-bottom: 1.5rem;
        color: var(--primary-dark);
    }

    .input-group {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        border: 1px solid #ccc;
        border-radius: 8px;
        overflow: hidden;
        transition: border-color 0.3s;
    }

    .input-group:focus-within {
        border-color: var(--primary-dark);
    }

    .form-floating {
        flex: 1;
        position: relative;
    }

    .form-floating input {
        width: 100%;
        border: none;
        padding: 1rem 0.75rem;
        outline: none;
        font-size: 1rem;
        border-radius: 0;
    }

    .form-floating label {
        position: absolute;
        top: 50%;
        left: 0.75rem;
        transform: translateY(-50%);
        color: #888;
        font-size: 0.9rem;
        pointer-events: none;
        transition: 0.3s;
    }

    .form-floating input:focus + label,
    .form-floating input:not(:placeholder-shown) + label {
        top: -0.5rem;
        left: 0.5rem;
        font-size: 0.75rem;
        background-color: #fff;
        padding: 0 0.25rem;
        color: var(--primary-dark);
    }

    .input-group-text {
        background-color: var(--primary);
        color: #fff;
        padding: 0 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-primary {
        background-color: var(--primary);
        border: none;
        color: #fff;
        padding: 0.75rem;
        font-size: 1rem;
        border-radius: 8px;
        transition: background-color 0.3s;
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: var(--primary-dark);
    }

    .row {
        margin-top: 1rem;
    }
</style>

<form action="{{route('admin.reset.password')}}" id="resetPasswordForm" method="post">
    @csrf
    <h2>Reset Password</h2>
    <input type="hidden" value="{{$email}}" name="email" required />
    
    <div class="input-group mb-1">
        <div class="form-floating"> 
            <input type="password" name="password" class="form-control" placeholder="Password" required> 
            <label>Password</label> 
        </div>
        <div class="input-group-text"> <span class="bi bi-lock-fill"></span> </div>
    </div>

    <div class="input-group mb-1">
        <div class="form-floating"> 
            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required> 
            <label>Confirm Password</label> 
        </div>
        <div class="input-group-text"> <span class="bi bi-lock-fill"></span> </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-grid gap-2"> 
                <button type="submit" class="btn btn-primary">Reset Password</button> 
            </div>
        </div>
    </div>
</form>
