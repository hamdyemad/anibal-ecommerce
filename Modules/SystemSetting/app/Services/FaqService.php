<?php

namespace Modules\SystemSetting\app\Services;

use Modules\SystemSetting\app\Repositories\FaqRepository;

class FaqService
{
    protected $faqRepository;

    public function __construct(FaqRepository $faqRepository)
    {
        $this->faqRepository = $faqRepository;
    }

    public function getAllFaqs()
    {
        return $this->faqRepository->all();
    }

    public function getFaqById($id)
    {
        return $this->faqRepository->find($id);
    }

    public function createFaq(array $data)
    {
        return $this->faqRepository->create($data);
    }

    public function updateFaq($id, array $data)
    {
        return $this->faqRepository->update($id, $data);
    }

    public function deleteFaq($id)
    {
        return $this->faqRepository->delete($id);
    }

    public function filterFaqs($filters)
    {
        return $this->faqRepository->filter($filters);
    }
}
