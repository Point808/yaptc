                </div>
            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <p class="text-muted"><?php echo lang('SERVER_TIME') . ": " . $timenow . " - - - " . lang('SOFTWARE_VERSION') . ": " . $yaptc_version; ?></p>
            </div>
        </footer>
        <script src="<?php echo $yaptc_libweb; ?>ie10-viewport-bug-workaround.js"></script>
        <script>window.jQuery || document.write('<script src="<?php echo $yaptc_libweb; ?>jquery.min.js"><\/script>')</script>
        <script src="<?php echo $yaptc_libweb; ?>bootstrap.min.js"></script>
    </body>
</html>
