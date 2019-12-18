<?php

class zcAjaxAdminMessageStack extends base {

  public function messageStack()
  {
    global $messageStack;
    if ($messageStack->size > 0) {
      return([
        'modalMessageStack' => $messageStack->output()]);
    }
  }

}
