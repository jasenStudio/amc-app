---
paths:
  - 'app/Livewire/**'
---

# Livewire

## Feedback de validación en formularios del dashboard
Los formularios del dashboard (Service/Project/Tag/Post/User) muestran el badge de campo requerido con `:label:badge="__('required_field')"` en inputs/selects requeridos, validan en tiempo real con `wire:model.live.blur` en campos requeridos/patrón, añaden `#[Validate]` (vacío) a esas propiedades en el componente y renderizan `<x-dashboard.form-error-summary />` al inicio del form. El método `rules()` debe ser `protected` (no `private`) para que `#[Validate]` lo use vía validateOnly. No añadir `#[Validate]` a campos opcionales tipo `order` (integer) porque vacío dispara falso error.
