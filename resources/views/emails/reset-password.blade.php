<x-mail::message>
# Restablecer tu contraseña

Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en **La Despensa**.

Pulsa el botón para crear una nueva contraseña. El enlace expira en **60 minutos**.

<x-mail::button :url="$deepLink" color="green">
Restablecer contraseña
</x-mail::button>

Si el botón no funciona, copia este código en la app:

<x-mail::panel>
**{{ $token }}**
</x-mail::panel>

Si no solicitaste restablecer tu contraseña, ignora este correo — tu cuenta sigue segura.

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
