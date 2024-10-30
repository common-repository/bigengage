<form method="POST" id="bigengage-settings">
<?php $autoEmbed = $this->getSetting('auto_embed'); ?>
<div id="poststuff" class="wrap">
  <div id="post-body" class="metabox-holder columns-2">
    <div id="post-body-content">
      <h2>
        Settings | BigEngage
      </h2>

      <table class="wp-list-table widefat fixed posts settings-table">
        <tbody>
          <tr>
            <td class="inside-container" width="30%">
              <h3>Auto Embedding</h3>
              <p>If enabled, it will add blank div tags in your posts and pages so you can embed forms more easily.</p>
            </td>
            <td class="setting">
              <select name="auto_embed">
                <option value="yes"<?php if ($autoEmbed == 'yes') echo "selected=\"selected\""; ?>>Yes</option>
                <option value="no"<?php if ($autoEmbed == 'no' || empty($autoEmbed))  echo "selected=\"selected\""; ?>>No</option>
              </select>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <input type="submit" name="Save" value="Save Settings" class="button button-primary" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    
  </div>
</div>
</form>