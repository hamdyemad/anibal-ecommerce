# Google OAuth Login Setup Guide

## ✅ COMPLETED STEPS

### 1. Laravel Socialite Installation
Laravel Socialite package has been installed successfully via Composer.

### 2. Database Migration
- Created migration: `2026_04_13_170248_add_google_fields_to_customers_table.php`
- Added `google_id` (string, nullable, unique) column to customers table
- Added `avatar` (string, nullable) column to customers table
- Migration has been run successfully

### 3. Customer Model Update
- Updated `Modules/Customer/app/Models/Customer.php`
- Changed from `$guarded = []` to explicit `$fillable` array
- Added `google_id` and `avatar` to fillable fields

### 4. Translation Keys
Added to `Modules/Customer/lang/en/customer.php`:
- `login_successful` - "Login successful"
- `account_inactive` - "Your account is inactive. Please contact support."
- `google_login_failed` - "Google login failed"

Added to `Modules/Customer/lang/ar/customer.php`:
- `login_successful` - "تم تسجيل الدخول بنجاح"
- `account_inactive` - "حسابك غير نشط. يرجى الاتصال بالدعم."
- `google_login_failed` - "فشل تسجيل الدخول عبر جوجل"

### 5. Google OAuth Controller
Created `Modules/Customer/app/Http/Controllers/Api/GoogleAuthController.php` with three methods:
- `redirectToGoogle()` - For web redirect flow
- `handleGoogleCallback()` - For web callback handling
- `loginWithGoogle()` - For mobile/SPA apps using access token

### 6. API Routes
Added to `Modules/Customer/routes/api.php`:
- `GET /api/v1/auth/google/redirect`
- `GET /api/v1/auth/google/callback`
- `POST /api/v1/auth/google/login`

### 7. Configuration
- Updated `config/services.php` with Google OAuth configuration
- Added Google OAuth variables to `.env.example`

---

## 🔧 REMAINING SETUP (User Action Required)

### Step 1: Get Google OAuth Credentials

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. Enable "Google+ API" or "Google Identity Services"
4. Go to "Credentials" → "Create Credentials" → "OAuth 2.0 Client ID"
5. Configure OAuth consent screen (if not already done):
   - User Type: External
   - App name: Your app name
   - User support email: Your email
   - Developer contact: Your email
6. Create OAuth 2.0 Client ID:
   - Application type: Web application
   - Name: Your app name
   - Authorized redirect URIs: 
     - `http://127.0.0.1:8000/api/v1/auth/google/callback` (for local testing)
     - `https://your-domain.com/api/v1/auth/google/callback` (for production)
7. Click "Create" and copy your Client ID and Client Secret

### Step 2: Add Credentials to .env

Add these lines to your `.env` file (replace with your actual credentials):

```env
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/api/v1/auth/google/callback
```

For production, update the redirect URI:
```env
GOOGLE_REDIRECT_URI=https://your-domain.com/api/v1/auth/google/callback
```

---

## 📱 API ENDPOINTS

### For Mobile/SPA Apps (Recommended)

**Endpoint:** `POST /api/v1/auth/google/login`

**Request Body:**
```json
{
  "access_token": "google_access_token_from_mobile_app"
}
```

**Success Response:**
```json
{
  "status": true,
  "message": "Login successful",
  "data": {
    "customer": {
      "id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "email": "john@example.com",
      "avatar": "https://lh3.googleusercontent.com/...",
      "google_id": "1234567890"
    },
    "token": "1|sanctum_token_here"
  }
}
```

**Error Response:**
```json
{
  "status": false,
  "message": "Google login failed: Invalid credentials",
  "data": [],
  "errors": []
}
```

### For Web (Redirect Flow)

**Step 1:** Redirect user to Google
```
GET /api/v1/auth/google/redirect
```

**Step 2:** Google redirects back to callback
```
GET /api/v1/auth/google/callback
```

This will return the same success response as the mobile endpoint.

---

## 🔧 MOBILE APP INTEGRATION

### React Native Example

```javascript
import { GoogleSignin } from '@react-native-google-signin/google-signin';

// Configure Google Sign-In
GoogleSignin.configure({
  webClientId: 'YOUR_GOOGLE_CLIENT_ID',
});

// Sign in with Google
const signInWithGoogle = async () => {
  try {
    await GoogleSignin.hasPlayServices();
    const userInfo = await GoogleSignin.signIn();
    const tokens = await GoogleSignin.getTokens();
    
    // Send access token to your API
    const response = await fetch('http://your-api.com/api/v1/auth/google/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        access_token: tokens.accessToken,
      }),
    });
    
    const data = await response.json();
    
    if (data.status) {
      // Save the Sanctum token
      await AsyncStorage.setItem('auth_token', data.data.token);
      // Navigate to home screen
    }
  } catch (error) {
    console.error(error);
  }
};
```

### Flutter Example

```dart
import 'package:google_sign_in/google_sign_in.dart';
import 'package:http/http.dart' as http;

final GoogleSignIn _googleSignIn = GoogleSignIn(
  scopes: ['email'],
);

Future<void> signInWithGoogle() async {
  try {
    final GoogleSignInAccount? googleUser = await _googleSignIn.signIn();
    final GoogleSignInAuthentication googleAuth = await googleUser!.authentication;
    
    // Send access token to your API
    final response = await http.post(
      Uri.parse('http://your-api.com/api/v1/auth/google/login'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({
        'access_token': googleAuth.accessToken,
      }),
    );
    
    final data = jsonDecode(response.body);
    
    if (data['status']) {
      // Save the Sanctum token
      await storage.write(key: 'auth_token', value: data['data']['token']);
      // Navigate to home screen
    }
  } catch (error) {
    print(error);
  }
}
```

---

## 🧪 TESTING

### Test with Postman

1. **Get Google Access Token** (for testing mobile flow):
   - Use Google OAuth 2.0 Playground: https://developers.google.com/oauthplayground/
   - Select "Google OAuth2 API v2" → "userinfo.email" and "userinfo.profile"
   - Click "Authorize APIs"
   - Exchange authorization code for tokens
   - Copy the "Access token"

2. **Test the Login Endpoint**:
   ```
   POST http://127.0.0.1:8000/api/v1/auth/google/login
   Content-Type: application/json
   
   {
     "access_token": "ya29.a0AfH6SMBx..."
   }
   ```

### Test Web Redirect Flow

Simply visit in your browser:
```
http://127.0.0.1:8000/api/v1/auth/google/redirect
```

This will redirect you to Google login, and after successful authentication, redirect back to your callback URL with the user data and token.

---

## 🔒 SECURITY NOTES

1. **Always use HTTPS in production** - Never send tokens over HTTP
2. **Keep your Client Secret secure** - Never expose it in client-side code
3. **Validate tokens on the server side** - Don't trust client-provided data
4. **Set appropriate token expiration times** - Use Sanctum's token expiration features
5. **Use CORS properly** - Configure allowed origins in `config/cors.php`
6. **Rate limit authentication endpoints** - Already configured with `throttle:auth` middleware

---

## 🎯 HOW IT WORKS

### Account Creation Flow

1. User signs in with Google (mobile app or web)
2. Google returns user information (email, name, avatar, google_id)
3. System checks if customer exists by email:
   - **If exists**: Updates `google_id` and `avatar` if not already set
   - **If new**: Creates new customer account with:
     - Name from Google
     - Email from Google (marked as verified)
     - Google ID
     - Avatar URL
     - Random password (user won't need it)
     - Active status
     - Default country (Egypt - ID: 1)
4. System creates Sanctum authentication token
5. Returns customer data and token to client

### Subsequent Logins

- User signs in with Google
- System finds existing customer by email
- Updates Google ID/avatar if needed
- Creates new Sanctum token
- Returns customer data and token

---

## ❓ TROUBLESHOOTING

### Error: "Invalid credentials"
- Check that your Google Client ID and Secret are correct in `.env`
- Verify the redirect URI matches exactly in Google Console and `.env`

### Error: "redirect_uri_mismatch"
- The redirect URI in your Google Console must match exactly
- Include the protocol (http/https) and port number

### Error: "Access blocked: This app's request is invalid"
- Your OAuth consent screen might not be configured
- Make sure you've added test users if in testing mode

### Error: "Token has been expired or revoked"
- The Google access token has expired (they expire after 1 hour)
- Get a new access token from Google

### Customer account is inactive
- Check the `status` field in the customers table
- New Google accounts are created with `status = true` by default

---

## 📝 NOTES

- Google emails are automatically marked as verified (`email_verified_at` is set)
- New customers are created with `status = true` (active)
- Default country is set to Egypt (ID: 1) - adjust in controller if needed
- Random password is generated for Google accounts (users won't need it)
- Avatar URL is stored directly from Google (not downloaded)
- Google ID is unique per customer
- Existing customers can link their Google account by logging in with Google using the same email
