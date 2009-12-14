From: Jiang Xin <worldhello.net@gmail.com>
Subject: [PATCH] t/all

A dummy patch, which depends on all other patches.

Signed-off-by: Jiang Xin <worldhello.net@gmail.com>

---
 config.inc.php |    2 +-
 1 files changed, 1 insertions(+), 1 deletions(-)

diff --git a/config.inc.php b/config.inc.php
index 2eeb83b..ae4ce20 100644
--- a/config.inc.php
+++ b/config.inc.php
@@ -139,7 +139,7 @@ $tlCfg->gui_title_separator_2 = ' - '; // parent - child
 $tlCfg->testcase_cfg->glue_character = '-';
 
 // used to draw charts:
-$tlCfg->charts_font_path = TL_ABS_PATH . "third_party/pchart/Fonts/tahoma.ttf";
+$tlCfg->charts_font_path = TL_ABS_PATH . "third_party/pchart/Fonts/simsun.ttc";
 $tlCfg->charts_font_size = 8;
 
 
-- 
tg: (9e7665a..) t/all (depends on: t/zh_cn_l10n t/custom_config t/bugtrac_integration t/single_signon t/fckeditor t/charts_in_chinese)
