<x-mail::message>

<div style="text-align: center; padding: 20px 0;">
<img src="{{ asset('favicon.png') }}" width="80" height="80" alt="BookMe Logo">
</div>

# {{ __('Business registration confirmation') }}

{{ __('Hello') }},

{{ __('Your business **:name** has been successfully registered in the BookMe system.', ['name' => $profile->name]) }}

{{ __('As a bonus, you get **the first 3 months of using the system completely for free**. Your free period ends on :date. After that, the system will be charged 20 € per month.', ['date' => $profile->trial_ends_at->format('d.m.Y')]) }}

{{ __('It is currently waiting for admin approval. After approval, your business will be publicly searchable on our main page.') }}

**{{ __('Until then, your profile is fully functional!') }}** {{ __('You can share it with your customers via this unique link:') }}

<x-mail::button :url="route('profiles.show', $profile->slug)">
{{ __('View my profile') }}
</x-mail::button>

{{ __('Your booking link:') }} [{{ route('profiles.show', $profile->slug) }}]({{ route('profiles.show', $profile->slug) }})

{{ __('You can log in to your dashboard and start setting up services, employees, and working hours:') }}

<x-mail::button :url="route('auth.login')">
{{ __('Log in to the system') }}
</x-mail::button>

---

### 🛠 {{ __('Guide to managing your business') }}

{{ __('To allow your business to fully accept bookings, we recommend the following procedure:') }}

1. **{{ __('Services (Services section)') }}**
{{ __('Create a list of services you offer. For each service, enter a name, duration, and price. Without created services, customers will not be able to create a booking.') }}

2. **{{ __('Employees (Employees section)') }}**
{{ __('Add members of your team. You can then assign specific services to each employee.') }}

3. **{{ __('Working hours (Times section)') }}**
{{ __('Set the times when you are available for clients. You can define general opening hours or individual schedules for each employee, including breaks.') }}

4. **{{ __('Holidays and closures (Holidays section)') }}**
{{ __('If you need to block a slot once (e.g., vacation or doctor visit), use this section to create a blockage.') }}

5. **{{ __('Appearance and settings (Calendar section)') }}**
{{ __('Upload a logo and business banner, write a short description, and adjust the length of booking slots (e.g., every 30 minutes).') }}

6. **{{ __('Dashboard (Overview)') }}**
{{ __('On the main screen, you will see all upcoming bookings, an interactive calendar for the selected day, and quick statistics. You can move, edit, or mark bookings as completed.') }}

7. **{{ __('Payments (Payment Overview)') }}**
{{ __('Detailed evaluation of your business - number of bookings, hours worked, and total revenue for the selected period.') }}

---

{{ __('We look forward to working with you!') }}

{{ __('Best regards,') }}<br>
{{ __('Team') }} {{ config('app.name') }}
</x-mail::message>
