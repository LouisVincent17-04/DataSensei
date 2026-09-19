{{--
    Default pagination view (Laravel resolves ->links() to pagination::tailwind).
    Every paginated list in DataSensei uses the same control as the admin
    workspace, styled by partials.design-system.
--}}
@include('vendor.pagination.admin')
