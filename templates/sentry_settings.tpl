{include file='header.tpl'}

<body id="page-top">

    <!-- Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        {include file='sidebar.tpl'}

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                {include file='navbar.tpl'}

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">{$SENTRY_SETTINGS}</h1>
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{$PANEL_INDEX}">{$DASHBOARD}</a></li>
                            <li class="breadcrumb-item"><a href="{$MODULES_URL}">{$MODULES}</a></li>
                            <li class="breadcrumb-item active">{$SENTRY_SETTINGS}</li>
                        </ol>
                    </div>

                    <!-- Update Notification -->
                    {include file='includes/update.tpl'}

                    <div class="card shadow mb-4">
                        <div class="card-body">
                            
                            {if isset($SUCCESS)}
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    {$SUCCESS}
                                </div>
                            {/if}

                            {if isset($ERRORS) && count($ERRORS)}
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <ul class="list-unstyled mb-0">
                                        {foreach from=$ERRORS item=error}
                                            <li>{$error}</li>
                                        {/foreach}
                                    </ul>
                                </div>
                            {/if}

                            <form action="" method="post">
                                <div class="form-group">
                                    <label for="sentry_dsn">{$SENTRY_DSN} *</label>
                                    <input type="text" class="form-control" id="sentry_dsn" name="sentry_dsn" placeholder="https://your-dsn@sentry.io/project-id" value="{$SENTRY_DSN_VALUE}">
                                    <small class="form-text text-muted">{$SENTRY_DSN_HELP}</small>
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="enable_frontend" id="enable_frontend" value="1" {if $ENABLE_FRONTEND_VALUE}checked{/if}>
                                        <label class="form-check-label" for="enable_frontend">
                                            {$ENABLE_FRONTEND}
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Enable JavaScript error tracking and user feedback widget</small>
                                </div>

                                <div class="form-group">
                                    <input type="hidden" name="token" value="{$TOKEN}">
                                    <input type="submit" class="btn btn-primary" value="{$SUBMIT}">
                                </div>
                            </form>

                            <hr>

                            <h5>Integration Status</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Backend Integration</h6>
                                            <p class="card-text">
                                                {if $SENTRY_DSN_VALUE}
                                                    <span class="badge badge-success">Active</span>
                                                    PHP errors and exceptions are being tracked
                                                {else}
                                                    <span class="badge badge-warning">Inactive</span>
                                                    Configure DSN to enable
                                                {/if}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">Frontend Integration</h6>
                                            <p class="card-text">
                                                {if $ENABLE_FRONTEND_VALUE && $SENTRY_DSN_VALUE}
                                                    <span class="badge badge-success">Active</span>
                                                    JavaScript errors, session replay, and user feedback enabled
                                                {else}
                                                    <span class="badge badge-warning">Inactive</span>
                                                    Enable frontend integration and configure DSN
                                                {/if}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <h6>Features Included:</h6>
                                <ul>
                                    <li><strong>Error Tracking:</strong> Automatically captures PHP exceptions and JavaScript errors</li>
                                    <li><strong>Session Replay:</strong> Records user interactions (10% of sessions, 100% on errors)</li>
                                    <li><strong>User Feedback:</strong> Allows users to report bugs directly</li>
                                    <li><strong>Performance Monitoring:</strong> Tracks page load times and API responses</li>
                                    <li><strong>Smart Filtering:</strong> Only creates issues for ERROR level and above</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            {include file='footer.tpl'}

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    {include file='scripts.tpl'}

</body>

</html>
