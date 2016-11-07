<?php

/**
 * Paragraphs item container
 * Bundle:      quote
 * Context:     any
 * View mode:   any
 */
?>
<div class="<?php print $classes; ?>">
    <div class="container-fluid">
        <blockquote>
            <p>
                <?php print render($content); ?>
            </p>
        </blockquote>
    </div>
</div>
