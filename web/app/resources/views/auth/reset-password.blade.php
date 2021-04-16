<form action="{{route('password.update')}}" method = 'post'>

    <h4 style = 'text-align:center'>Create Your New Password</h4>
    <div class="form-group">
        <input type="hidden" id="token" name="token" value="{{$token}}"/>
        <input type="hidden" id="email" name="email" value="{{$email}}"/>
        <label for="password">New Password:
            <span>
                <input type="password" id="password" name="password"  class="form-control" placeholder="Enter Your Password Here" style = 'text-align:center'>
            </span>
        </label>
        <label for="password_confirmation">Confirm Your Password:
            <span>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirm Your Password Here" style = 'text-align:center'>
            </span>
        </label>
    </div>

    <div>
        <button type="submit" id="reset-password-submit" name="reset-password-submit" value="submit">
            Reset your password
        </button>
    </div>
</form>
