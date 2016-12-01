<?php
/**
 * MageParts
 *
 * NOTICE OF LICENSE
 *
 * This code is copyrighted by MageParts and may not be reproduced
 * and/or redistributed without a written permission by the copyright
 * owners. If you wish to modify and/or redistribute this file please
 * contact us at info@mageparts.com for confirmation before doing
 * so. Please note that you are free to modify this file for personal
 * use only.
 *
 * If you wish to make modifications to this file we advice you to use
 * the "local" file scope in order to aviod conflicts with future updates.
 * For information regarding modifications see http://www.magentocommerce.com.
 *
 * DISCLAIMER
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF
 * USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   MageParts
 * @package    MageParts_Base
 * @copyright  Copyright (c) 2009 MageParts (http://www.mageparts.com/)
 * @author     MageParts Crew
 */

class MageParts_Base_Helper_Ajax extends MageParts_Base_Helper_Data
{

    /**
     * Clear redirect on controller.
     *
     * @param $controller
     * @return $this
     */
    public function correctResponseHeaders(&$controller)
    {
        // avoid redirects
        $controller->getResponse()
            ->clearHeader('Location')
            ->setHttpResponseCode(200);

        // avoid forwards
        $controller->getRequest()->setDispatched(true);

        return $this;
    }

    /**
     * Transfer messages from sessions to referenced array.
     *
     * @param array $data
     * @param array $strict (only include specified message types).
     * @param boolean $clear (remove messages from sessions after collecting them).
     * @param array $sessions
     * @return $this
     */
    public function collectMessages(array &$data, $strict=array(), $clear=true, $sessions=array())
    {
        $this->fillSessionsArray($sessions);

        foreach ($sessions as $session) {
            $model = Mage::getSingleton($session);

            if ($model) {
                $messageCollection = $model->getData('messages');

                if ($messageCollection && count($messageCollection->getItems())) {
                    foreach ($messageCollection->getItems() as $message) {
                        if (!count($strict) || in_array($message->getType(), $strict)) {
                            if (!isset($data[$message->getType()])) {
                                $data[$message->getType()] = array();
                            }

                            if (isset($data[$message->getType()]) && is_array($data[$message->getType()]) && !in_array($message->getCode(), $data[$message->getType()])) {
                                $data[$message->getType()][] = $message->getCode();
                            }
                        }
                    }
                }
            }
        }

        if ($clear) {
            $this->clearSessionMessages($sessions);
        }

        return $this;
    }

    /**
     * Clear out all session messages.
     *
     * @param array $sessions
     * @return $this
     */
    public function clearSessionMessages($sessions=array())
    {
        $this->fillSessionsArray($sessions);

        foreach ($sessions as $session) {
            $model = Mage::getSingleton($session);

            if ($model) {
                $model->getData('messages')->clear();
            }
        }

        return $this;
    }

    /**
     * Fill passed sessions array.
     *
     * @param array $sessions
     * @return array
     */
    public function fillSessionsArray(array &$sessions)
    {
        if (!count($sessions)) {
            if ($this->isAdmin()) {
                $sessions = array(
                    'core/session',
                    'adminhtml/session'
                );
            } else {
                $sessions = array(
                    'core/session',
                    'customer/session',
                    'checkout/session'
                );
            }
        }

        return $sessions;
    }

    /**
     * Retrieve action result shell array.
     *
     * @return array
     */
    public function getActionResultShell()
    {
        return array(
            'msg' => array(
                'success'   => array(),
                'error'     => array(),
                'notice'    => array()
            ),
            'htmlUpdates' => array()
        );
    }

}
